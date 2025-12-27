<template>
    <main-layout>
        <div class="project-detail-wrapper">
            <div class="breadcrumb-and-header pb-6">
                <PageHeader title="Project Detail" :show-action-btn="projectDetail?.status != 'complete'"
                    :breadcrumbs="breadcrumbs" @action="makeProjectComplete" action-btn-name="Make Complete"
                    :action-btn-icon="IconLibraryPlusFilled" />
            </div>
            <div v-if="projectDetail && !loading" class="project-detail-wrapper grid grid-cols-5 gap-4">
                <!-- project info section start here  -->
                <div class="project-info col-span-2 bg-surface text-on-surface box-shadow rounded p-4">
                    <div class="project-general-info-card">
                        <h1 class="project-title text-lg font-semibold line-clamp-2"> {{ projectDetail.name }} </h1>
                        <p class="project-creator text-sm text-grey-600 pb-2"> Created by: {{ projectDetail.created_by
                        }} </p>
                        <p class="project-description text-justify leading-7"> {{ projectDetail.description }} </p>
                        <div class="project-progress pt-4">
                            <h1 class="status text-sm pb-2">Project Progress</h1>
                            <!-- <div class="progress-bar bg-background rounded-md relative">
                                <div :class="['progress bg-primary h-4.5', calculateProgress(projectDetail.completed_tasks, projectDetail.total_tasks) == 100 ? 'rounded-md' : 'rounded-l-md']"
                                    :style="{ width: `${calculateProgress(projectDetail.completed_tasks, projectDetail.total_tasks)}%` }">
                                    <span
                                        class="progress-percentage text-on-primary text-sm absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2">
                                        {{ calculateProgress(projectDetail.completed_tasks, projectDetail.total_tasks)
                                        }}%
                                    </span>
                                </div>
                            </div> -->
                            <div class=" flex justify-center items-center">
                                <AnimatedProgress
                                    :progress="calculateProgress(projectDetail.completed_tasks, projectDetail.total_tasks)" />
                            </div>
                        </div>
                    </div>
                    <div class="project-status-info">
                        <div class="project-status flex justify-between items-center pt-4">
                            <p class="status-label">Status:</p>
                            <p
                                :class="['status-value text-sm px-1 rounded capitalize', getTaskStatusColorClass(projectDetail.status)]">
                                {{
                                    projectDetail.status.replace('_', ' ') }}</p>
                        </div>
                        <!-- <div class="project-creator flex justify-between items-center pt-2">
                            <p class="creator-label">Priority:</p>
                            <p class="creator-name text-sm bg-error text-on-surface px-1 rounded">High</p>
                        </div> -->
                        <div class="project-collaborators flex justify-between items-center pt-2">
                            <p class="collaborators-label">Created At:</p>
                            <p class="collaborators-value">{{ projectDetail.created_at }}</p>
                        </div>
                        <div class="project-deadline flex justify-between items-center pt-2">
                            <p class="deadline-label">Deadline:</p>
                            <p class="deadline-value">{{ projectDetail.due_date }}</p>
                        </div>
                        <div class="project-deadline flex justify-between items-center pt-2">
                            <p class="deadline-label">Day Left:</p>
                            <p class="deadline-value">{{ calculateDuration(projectDetail.due_date) }}</p>
                        </div>
                    </div>
                </div>
                <!-- project info section end here  -->

                <!-- collaborator list section start here -->
                <div class="project-colaborator col-span-3 bg-surface text-on-surface box-shadow rounded p-4">
                    <h1 class="project-title text-lg font-semibold pb-2"> Collaborator List </h1>
                    <div class="collaborator-list flex flex-col gap-2 max-h-[360px] overflow-auto custom-scrollbar">
                        <div v-for="collaborator in projectDetail.collaborators" :key="collaborator.id"
                            class="flex items-center bg-hover rounded p-3">
                            <img :src="collaborator.profile_url" alt="Profile"
                                class="w-12 h-12 rounded-full mr-6 object-cover border-2 border-primary" />
                            <div class="flex-1 flex flex-row items-center justify-between">
                                <div>
                                    <div class="font-semibold text-base">{{ collaborator.name }}</div>
                                    <div class="text-sm text-grey-600 capitalize">{{ collaborator.role }}</div>
                                </div>
                                <div class="flex flex-col gap-y-2 text-right">
                                    <span class="text-xs text-grey-500">
                                        Total Collaboration: <span class="font-medium">{{ collaborator.total_projects ??
                                            0 }}</span>
                                    </span>
                                    <span class="text-xs text-grey-500">
                                        Tasks Completed: <span class="font-medium">{{ collaborator.total_tasks_complete
                                            ?? 0
                                        }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- collaborator list section end here -->

                <!-- project task section start here -->
                <div class="project-task-container col-span-3 bg-surface text-on-surface box-shadow rounded p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-lg font-semibold">Tasks</h1>
                        <SmallButton v-if="projectDetail.status != 'complete' && checkPermissions('task.create')"
                            name="New Task" @click="handleNewTask"
                            customClasses="bg-primary text-on-primary rounded py-1.5" :icon="IconLibraryPlusFilled" />
                    </div>
                    <div class="task-list grid grid-cols-2 gap-4 max-h-[400px] overflow-auto custom-scrollbar">
                        <!-- Task Card Example -->
                        <div v-for="task in projectDetail.tasks" :key="task.id"
                            class="task-card bg-hover rounded p-4 relative"
                            :class="[
                                calculateDuration(task.due_date) === 'Overdue'
                                ? ' border border-red-500'
                                : '',
                            ]">
                            <div class="task-card-title mb-2">
                                <div>
                                    <h2 class="task-title text-base font-semibold line-clamp-1">{{ task.name }}</h2>
                                    <p class="task-description text-sm text-grey-600">{{ task.description }}</p>
                                </div>
                                <div class="header-action flex absolute top-2 right-2">
                                    <IconEdit v-if="checkPermissions('task.edit')" @click="handleTaskEdit(task.id)"
                                        class="w-6 h-6 cursor-pointer text-grey-600" />
                                    <IconEye v-if="checkPermissions('task.detail')" @click="handleTaskView(task.id)"
                                        class="w-6 h-6 cursor-pointer text-grey-600" />
                                </div>
                            </div>

                            <div class="task-details mt-2">
                                <div class="project-status flex justify-between items-center py-1">
                                    <p class="status-label">Status:</p>
                                    <p
                                        :class="`status-value text-sm ${getTaskStatusColorClass(task.status)} px-1 rounded capitalize`">
                                        {{ task.status.replace('_', ' ') }}
                                    </p>
                                </div>
                                <div class="project-status flex justify-between items-center py-1">
                                    <p class="status-label">Collaborator:</p>
                                    <div>
                                        <p class="status-value text-sm text-green-500" v-for="(collaborator,index) in task?.collaborators" :key="index">{{ collaborator.name }}</p>
                                    </div>
                                </div>
                                <div class="project-status flex justify-between items-center py-1">
                                    <p class="status-label">Created By:</p>
                                    <p class="status-value text-sm">{{ task.created_by }}</p>
                                </div>
                                <div class="project-status flex justify-between items-center py-1">
                                    <!-- Left: Date -->
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-grey-500">Due Date:</span>
                                        <span class="text-xs font-medium"
                                            :class="[
                                            calculateDuration(task.due_date) === 'Overdue'
                                                ? 'text-red-500'
                                                : 'text-green-500',
                                            'deadline-value'
                                            ]">
                                            {{ task.due_date }}
                                        </span>
                                    </div>
                                    <!-- Right: Collaborator Avatars -->
                                    <div class="flex items-center gap-1 ml-auto">
                                        <div v-for="(collaborator, idx) in task.collaborators.slice(0, 3)"
                                            :key="collaborator.id + '-avatar'"
                                            class="w-6 h-6 rounded-full border-2 border-white -ml-2 first:ml-0 overflow-hidden">
                                            <img :src="collaborator.profile_url" :alt="collaborator.name"
                                                class="w-full h-full object-cover" />
                                        </div>
                                        <span v-if="task.collaborators.length > 3"
                                            class="w-6 h-6 rounded-full bg-grey-200 text-xs flex items-center justify-center -ml-2 border-2 border-white">
                                            +{{ task.collaborators.length - 3 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <DotLoading v-if="loading" :show-dots="false" />
                </div>
                <!-- project task section end here -->

                <!-- project comment section here  -->
                <Comment :comment-id="projectId" comment-type="project" />
            </div>
            <div v-else>
                <ProjectDetail />
            </div>
        </div>

        <!-- task create model section start here  -->
        <Model :title="modelTitle" :visible="openTaskAddModel" @dismiss="handleModelClose" @submit="handleModelSubmit"
            :submit-loading="submitLoading">
            <form class="project-add-form py-3">
                <div class="project-name py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Task Name
                    </label>
                    <InputBox v-model="taskForm.name" label="Task Name" placeholder="Please type task name"
                        type="text" />
                </div>

                <div class="project-deadline py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Task Deadline
                    </label>

                    <DatePicker v-model="taskForm.dueDate" :enable-time-picker="true" dark />
                </div>

                <div class="project-description py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Task Description
                    </label>
                    <Textarea v-model="taskForm.description" label="Task Description"
                        placeholder="Please type task description" type="text" />
                </div>

                <div class="project-collaborator pt-">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Collaborator
                    </label>
                    <SelectBox v-model="taskForm.colaborator" multiple :options="colaboratorOptions" />
                </div>
            </form>
            <DotLoading v-if="colaboratorLoading" />
        </Model>
        <!-- task create model section end here  -->
    </main-layout>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { IconLibraryPlusFilled, IconEdit, IconEye } from '@tabler/icons-vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/utils/api';
import { toast } from 'vue3-toastify';
import webSocket from '@/utils/websocket';
import { checkPermissions } from '@/utils/helper.js'
import AnimatedProgress from '@/components/common/AnimatedProgress.vue';
import ProjectDetail from '../../loading/ProjectDetail.vue';

const authUser = JSON.parse(localStorage.getItem('user'));
const userId = authUser ? authUser.id : null;

const loading = ref(false);
const route = useRoute();
const router = useRouter();
const projectDetail = ref(null);
const projectId = ref(route.params.id);
const openTaskAddModel = ref(false);
const isTaskEditing = ref(false);

const colaboratorOptions = ref([]);
const colaboratorLoading = ref(false);
const submitLoading = ref(false);
const taskDetailLoading = ref(false);

const modelTitle = ref('Add New Task');

const taskForm = reactive({
    name: '',
    description: '',
    dueDate: null,
    colaborator: []
});

onMounted(() => {
    fetchProjectDetail();
    projectStatusChangeListen();
    tasksStatusChangeListen();
    taskCreated();
});

const projectStatusChangeListen = () => {
    let channelInfo = {
        channel_name: `project.${projectId.value}`,
        event_name: '.UpdateProject'
    }

    webSocket.privateListen(channelInfo, (data) => {
        console.log('project update socket response => ', data)
    })
}

const tasksStatusChangeListen = () => {
    let channelInfo = {
        channel_name: `project.${projectId.value}`,
        event_name: '.TaskStatusChange'
    }

    webSocket.privateListen(channelInfo, (data) => {
        console.log('task status change socket response => ', data)
        const updatedTask = data.data;
        const taskIndex = projectDetail.value.tasks.findIndex(task => task.id === updatedTask.id);
        if (taskIndex !== -1) {
            projectDetail.value.tasks[taskIndex] = updatedTask;
            projectDetail.value.completed_tasks += data.data.status === 'completed' ?? 1
        } else {
            projectDetail.value.tasks.push(updatedTask);
        }
    })
}

const taskCreated = () => {
    let channelInfo = {
        channel_name: `project.${projectId.value}.user.${userId}.tasks`,
        event_name: '.task.created'
    }

    webSocket.privateListen(channelInfo, (data) => {
        console.log('task created socket response => ', data.data);
        projectDetail.value.tasks.push(data.data);
        projectDetail.value.total_tasks += 1;
    })
}


const fetchProjectDetail = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/project/detail-with-tasks/${projectId.value}`);
        projectDetail.value = response.data.data;
    } catch (error) {
        console.error('Error fetching project detail:', error);
    } finally {
        loading.value = false;
    }
};

const breadcrumbs = [
    {
        name: "Home",
        route: "/",
    },
    {
        name: "ProjectList",
        route: "/project/list",
    },
    {
        name: "Project Detail",
        route: "",
    },
];

const handleNewTask = () => {
    fetchColaboratorOptions();
    openTaskAddModel.value = true;
};

const reset = () => {
    taskForm.name = '';
    taskForm.description = '';
    taskForm.dueDate = null;
    taskForm.colaborator = [];
};

const handleModelClose = (visible) => {
    openTaskAddModel.value = visible;
    reset();
};

const handleTaskEdit = (id) => {
    modelTitle.value = 'Edit Task';
    fetchColaboratorOptions();
    fetchTaskDetail(id);

}

const fetchTaskDetail = async (id) => {
    taskDetailLoading.value = true;
    try {
        const response = await api.get(`/task/detail/${id}`);
        taskForm.name = response.data.data.name;
        taskForm.description = response.data.data.description;
        taskForm.dueDate = response.data.data.due_date;
        taskForm.colaborator = response.data.data.collaborators.map(item => ({ value: item.id, label: item.name }));
        openTaskAddModel.value = true;
        isTaskEditing.value = true;
    } catch (error) {
        console.error('Error fetching task detail:', error);
    } finally {
        taskDetailLoading.value = false;
    }
};

const handleModelSubmit = () => {
    if (isTaskEditing.value) {
        updateTask();
    } else {
        storeTask();
    }
};

const storeTask = () => {
    submitLoading.value = true;
    api.post('/task/store', transformTaskPayload()).then((res) => {
        openTaskAddModel.value = false
        reset()
        submitLoading.value = false;
        fetchProjectDetail();
    }).catch((error) => {
        console.log('Error storing task:', error);
        let errors = error?.response?.data?.errors;
        if (errors) {
            Object.values(errors).map((error) => {
                toast(error, {
                    autoClose: 2000,
                    type: 'error'
                })
            })
        } else {
            toast(error?.response?.data?.message || 'Something went wrong', {
                autoClose: 2000,
                type: 'error'
            })
        }

        openTaskAddModel.value = false
    })
}

const updateTask = () => {
    submitLoading.value = true;
    api.post(`/task/update/${projectId.value}`, transformTaskPayload()).then((res) => {
        openTaskAddModel.value = false
        reset()
        isTaskEditing.value = false;
        submitLoading.value = false;
        fetchProjectDetail();
    }).catch((error) => {
        let errors = error?.response?.data?.errors;
        if (errors) {
            Object.values(errors).map((error) => {
                toast(error, {
                    autoClose: 2000,
                    type: 'error'
                })
            })
        }
    })
}

const transformTaskPayload = () => {
    return {
        name: taskForm.name,
        description: taskForm.description,
        collaborators: taskForm.colaborator.map(item => item.value),
        due_date: taskForm.dueDate,
        project_id: projectId.value
    }
}

const fetchColaboratorOptions = async () => {
    colaboratorLoading.value = true;
    try {
        const response = await api.get(`/project/colaborator/list/${projectId.value}`);
        colaboratorOptions.value = response.data.data.map(colaborator => ({
            label: colaborator.name,
            value: colaborator.id
        }));
    } catch (error) {
        console.error('Error fetching collaborators:', error);
    } finally {
        colaboratorLoading.value = false;
    }
};

const makeProjectComplete = () => {
    api.post(`/project/make-complete/${projectId.value}`).then((res) => {
        toast(res.data.message, {
            autoClose: 2000,
            type: 'success'
        });
        fetchProjectDetail();
    }).catch((error) => {
        let errorMsg = error?.response?.data?.message;
        if (errorMsg) {
            toast(errorMsg, {
                autoClose: 2000,
                type: 'error'
            })
        }
    });
};

const handleTaskView = (id) => {
    router.push({
        name: 'task-detail',
        params: { id }
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
            return 'bg-secondary text-on-surface';
        default:
            return 'bg-grey-200 text-grey-600';
    }
};

const calculateDuration = (dueDate) => {
    const today = new Date();
    const due = new Date(dueDate);
    const diffTime = due - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays >= 0 ? `${diffDays} days left` : 'Overdue';
};

const calculateProgress = (completedTasks, totalTasks) => {
    if (totalTasks === 0) return 0;
    return Math.round((completedTasks / totalTasks) * 100);
};

</script>
