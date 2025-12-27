<template>
    <div :class="['max-w-xl mx-auto mt-2 font-sans mb-4', isLast ? '' : 'border-b border-hover']">
        <div class="flex items-start mb-2">
            <img :src="comment.profile" alt="avatar"
                class="w-12 h-12 rounded-full mr-4 object-cover border-2 border-primary" />
            <div class="flex-1 rounded relative">
                <div class="flex items-center mb-1">
                    <span class="font-semibold text-base">{{ comment.name }}</span>
                    <span class="text-xs text-grey-500 ml-3">{{ timeAgo(comment.created_at) }}</span>
                </div>
                <div class="mb-2 text-grey-600 text-sm">{{ comment.content }}</div>
                <div class="mb-1 flex justify-start gap-x-6">
                    <span class="reply-count bg-grey-100 text-grey-700 px-2 py-0.5 rounded text-sm font-semibold mr-2"
                        v-if="comment.replies && comment.replies.length">
                        {{ comment.replies.length }} {{ getReplyLabel(comment.replies.length) }}
                    </span>
                    <span v-else
                        class="reply-count bg-grey-100 text-grey-700 px-2 py-0.5 rounded text-sm font-semibold mr-2"> 0
                        reply</span>
                    <button v-if="authUser?.id != comment.user_id"
                        class=" text-primary hover:underline text-sm font-medium cursor-pointer"
                        @click="toggleReply">Reply</button>
                </div>
                <div v-if="showReply" class="flex gap-2 mt-2">
                    <input v-model="replyText" placeholder="Write a reply..." @keyup.enter="submitReply(comment.id)"
                        class="flex-1 px-3 py-1.5 rounded border border-grey-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    <button @click="submitReply(comment.id)"
                        class="bg-primary hover:bg-primary-dark text-white rounded px-4 py-1.5 text-sm font-medium cursor-pointer">Send</button>
                </div>
            </div>
        </div>
        <div v-if="comment.replies && comment.replies.length" class="ml-12">
            <div v-for="reply in comment.replies" :key="reply.id" class="flex items-start mt-2 mb-5">
                <img :src="reply.profile" alt="avatar"
                    class="w-10 h-10 rounded-full mr-3 object-cover border-2 border-primary" />
                <div class="flex-1 ">
                    <div class="flex items-center mb-1">
                        <span class="font-semibold text-sm">{{ reply.name }}</span>
                        <span class="text-xs text-grey-500 ml-2">{{ timeAgo(reply.created_at) }}</span>
                    </div>
                    <div class="text-grey-600 text-sm">{{ reply.content }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { timeAgo } from '../utils/helper';

const props = defineProps({
    comment: {
        type: Object,
        required: true
    },
    isLast: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(['submitReply']);
const authUser = ref(JSON.parse(localStorage.getItem('user')));

const showReply = ref(false);
const replyText = ref('');

function toggleReply() {
    showReply.value = !showReply.value;
}

const getReplyLabel = (length) => {
    return length > 1 ? 'replies' : 'reply'
}

const submitReply = (id) => {
    if (replyText.value.length < 0) return;

    emit('submitReply', {
        id: id,
        reply: replyText.value
    })

    replyText.value = '';
    showReply.value = false;
}
</script>
