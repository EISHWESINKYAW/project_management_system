<template>
    <div class="task-edit-wrapper pb-3">
        <div class="breadcrumb-and-header pb-6">
            <PageHeader title="Edit Task" :breadcrumbs="breadcrumbs" :show-action-btn="false"
                :permission="'task.create'" />
        </div>
        <div class="flex justify-end gap-x-4 pb-4 pe-5">
            <SmallButton v-if="taskForm.status === 'pending'" name="Start" @click="updateTask('in_progress')"
                :is-loading="loading.submit" customClasses="bg-primary text-on-primary rounded py-1.5"
                :icon="IconSkateboarding" />

            <SmallButton v-if="taskForm.status === 'in_progress'" name="Complete" @click="updateTask('completed')"
                :is-loading="loading.submit" customClasses="bg-primary text-on-primary rounded py-1.5"
                :icon="IconAwardFilled" />
        </div>
        <div class="grid grid-cols-2 gap-x-3">
            <TaskDetailCard v-if="loading.detail" />
            <TaskInfoCard v-else :task="taskDetail" />
            <div class="task-comment-section">
                <CommentCard v-if="loading.detail" />
                <Comment v-else :comment-id="taskId" comment-type="task" />
            </div>
        </div>
        <DotLoading v-if="loading.submit" :show-dots="false" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, } from 'vue';
import api from '@/utils/api';
import { useRoute } from 'vue-router';
import { toast } from 'vue3-toastify';
import { IconSkateboarding, IconAwardFilled } from '@tabler/icons-vue';
import webSocket from '@/utils/websocket';
import TaskInfoCard from '../../components/TaskInfoCard.vue';
import TaskDetailCard from '../../loading/TaskDetailCard.vue';
import CommentCard from '../../loading/CommentCard.vue';

const projectOptions = ref([]);
const route = useRoute();
const taskId = ref(route.params.id);
const initialProjectId = ref(null);
const taskDetail = ref(null);

const loading = reactive({
    colaborator: false,
    submit: false,
    detail: false
})

onMounted(() => {
    fetchTaskDetail(taskId.value);
});

const breadcrumbs = [
    {
        name: "Home",
        route: "/",
    },
    {
        name: "TaskList",
        route: "/task/list",
    },
    {
        name: "Task Detail",
        route: "",
    },
];

const taskForm = reactive({
    name: '',
    description: '',
    dueDate: null,
    creator: null,
    createdAt: null,
    status: null,
    colaborator: [],
    project: []
});


const fetchTaskDetail = (id) => {
    loading.detail = true;
    api.get(`/task/detail/${id}`).then((res) => {
        let response = res?.data?.data;
        taskDetail.value = response;
        taskForm.description = response.description
        taskForm.name = response.name
        taskForm.dueDate = response.due_date
        taskId.value = response?.id;
        taskForm.status = response.status
        taskForm.createdAt = response.created_at
        taskForm.creator = response.created_by
        taskForm.colaborator = response.collaborators
        taskForm.project = [{
            value: response.project.id,
            label: response.project.name
        }]
        initialProjectId.value = response.project.id;
        tasksStatusChangeListen();
        loading.detail = false
    }).catch((error) => {
        console.error(error);
    })

}

const updateTask = (status) => {
    loading.submit = true;
    const payload = {
        status: status,
    };

    api.post(`/task/update-status/${taskId.value}`, payload).then((res) => {
        toast.success(res.data.message);
        loading.submit = false;
        fetchTaskDetail(taskId.value);
    }).catch((error) => {
        console.error(error);
        toast.error(error.response.data.message || "Something went wrong");
        loading.submit = false;
    });
};

const tasksStatusChangeListen = () => {
    let channelInfo = {
        channel_name: `project.${initialProjectId.value}`,
        event_name: '.TaskStatusChange'
    }

    webSocket.privateListen(channelInfo, (data) => {
        console.log('task status change socket response => ', data)
        let response = data?.data;
        if (response.id !== taskId.value) {
            return;
        }
        taskForm.status = response.status;
    })
}

</script>
