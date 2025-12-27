<template>
    <div class="task-container col-span-2 bg-surface text-on-surface box-shadow rounded p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-lg font-semibold">Comments</h1>
            <SmallButton @click="handleNewTask" customClasses="bg-primary text-on-primary rounded py-1.5"
                :icon="IconMessagePlus" />
        </div>
        <div v-if="showCommentInput" class="flex gap-2 my-3">
            <input v-model="commentText" placeholder="Write a comment..." @keyup.enter="addComment"
                class="flex-1 px-3 py-1.5 rounded border border-grey-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
            <button @click="addComment"
                class="bg-primary hover:bg-primary-dark text-white rounded px-4 py-1.5 text-sm font-medium cursor-pointer">Send</button>
        </div>
        <div class="task-list grid grid-cols-1 gap-4 max-h-[400px] overflow-auto custom-scrollbar">
            <div v-if="comments.length == 0" class="comment-empty-msg flex items-center justify-center">
                No Comments yet!
            </div>
            <div v-else class="comment-cards">
                <!-- Task Card Example -->
                <div v-for="(comment, index) in comments" class="comment-section">
                    <CommentCard :isLast="index == comments.length - 1" :comment="comment"
                        @submit-reply="submitReply" />
                </div>
            </div>
        </div>
        <DotLoading v-if="loading" :show-dots="false" />
    </div>
</template>

<script setup>
import { IconMessagePlus } from '@tabler/icons-vue';
import { onMounted, ref } from 'vue';
import { checkPermissions } from '@/utils/helper.js'
import CommentCard from './CommentCard.vue';
import api from '@/utils/api';
import { toast } from 'vue3-toastify';

const props = defineProps({
    commentId: {
        type: Number,
        required: true
    },
    commentType: {
        type: String,
        required: true
    }
})

const showCommentInput = ref(false);
const commentLoading = ref(false);
const commentText = ref('');
const comments = ref([]);

onMounted(() => {
    fetchComments();
})

const fetchComments = async () => {
    commentLoading.value = true;
    try {
        const response = await api.get(`/comment/commentable/${props.commentId}`, {
            params: {
                type: props.commentType
            }
        });
        comments.value = response?.data?.data;

    } catch (error) {
        console.error('Error fetching comments list:', error);
    } finally {
        commentLoading.value = false;
    }
}

const handleNewTask = () => {
    showCommentInput.value = !showCommentInput.value
}

const submitReply = ({ id, reply }) => {
    if (id && reply) {
        addComment(id, reply)
    }
}

const addComment = (parentId = null, replyText = null) => {
    api.post(`/comment/commentable/${props.commentId}`, transformCommentPayload(parentId, replyText))
        .then((res) => {
            toast(res.data.message, {
                autoClose: 2000,
                type: 'success'
            });
            if (res.status == 201) {
                fetchComments()
            }
            showCommentInput.value = false;
            commentText.value = ''
        }).catch((error) => {
            let errorMsg = error?.response?.data?.message;
            if (errorMsg) {
                toast(errorMsg, {
                    autoClose: 2000,
                    type: 'error'
                })
            }
        });
}

const transformCommentPayload = (parentId = null, replyText = null) => {
    if (parentId && replyText) {
        return {
            parent_id: parentId,
            content: replyText,
            type: props.commentType
        }
    }

    return {
        content: commentText.value,
        type: props.commentType
    }
}
</script>
