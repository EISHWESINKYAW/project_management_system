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
        <div class="task-edit-form-wrapper bg-surface text-on-surface px-5 py-3 box-shadow rounded">
            <form class="task-edit-form grid grid-cols-2 gap-4">
                <div class="col-span-2 grid grid-cols-5 gap-x-4">
                    <div class="task-name py-2 col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                            Task Status
                        </label>
                        <p
                            :class="['status-value text-sm px-2 py-1 w-fit rounded capitalize', getTaskStatusColorClass(taskForm.status)]">
                            {{
                                taskForm?.status?.replace('_', ' ') }}</p>
                    </div>
                    <div class="task-name py-2 col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                            Task Created At
                        </label>
                        <InputBox v-model="taskForm.createdAt" label="Task Created At" disabled
                            placeholder="Please type task created at" type="text" />
                    </div>
                    <div class="task-deadline py-2 col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                            Task Deadline
                        </label>

                        <InputBox v-model="taskForm.dueDate" label="Task Created At" disabled
                            placeholder="Please type task created at" type="text" />
                    </div>

                </div>
                <div class="task-name py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Task Creator
                    </label>
                    <InputBox v-model="taskForm.creator" label="Task Name" disabled placeholder="Please type task name"
                        type="text" />
                </div>
                <div class="task-name py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Task Name
                    </label>
                    <InputBox v-model="taskForm.name" label="Task Name" disabled placeholder="Please type task name"
                        type="text" />
                </div>

                <div class="task-project py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Project
                    </label>
                    <SelectBox disabled v-model="taskForm.project" :options="projectOptions" />
                </div>

                <div class="task-collaborator py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Collaborator
                    </label>
                    <!-- <SelectBox v-model="taskForm.colaborator" disabled multiple :options="colaboratorOptions" /> -->
                    <div class="flex items-center gap-1 ml-auto">
                        <div v-for="(collaborator, idx) in taskForm.colaborator.slice(0, 3)"
                            :key="collaborator.id + '-avatar'"
                            class="w-10 h-10 rounded-full border-2 border-white -ml-2 first:ml-0 overflow-hidden">
                            <img :src="collaborator.profile" :alt="collaborator.name"
                                class="w-full h-full object-cover" />
                        </div>
                        <span v-if="taskForm.colaborator.length > 3"
                            class="w-6 h-6 rounded-full bg-grey-200 text-xs flex items-center justify-center -ml-2 border-2 border-white">
                            +{{ taskForm.colaborator.length - 3 }}
                        </span>
                    </div>

                </div>

                <div class="task-description col-span-2 py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Task Description
                    </label>
                    <Textarea v-model="taskForm.description" disabled label="Task Description"
                        placeholder="Please type task description" type="text" />
                </div>
            </form>
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

const colaboratorOptions = ref([]);
const projectOptions = ref([]);
const route = useRoute();
const taskId = ref(route.params.id);
const initialProjectId = ref(null);

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

const getTaskStatusColorClass = (status) => {
    switch (status) {
        case 'in_progress':
            return 'bg-warning text-on-warning';
        case 'completed':
            return 'bg-success text-on-success';
        case 'overdue':
            return 'bg-error text-on-surface';
        case 'pending':
            return 'bg-secondary text-on-secondary';
        default:
            return 'bg-grey-200 text-grey-600';
    }
};

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
