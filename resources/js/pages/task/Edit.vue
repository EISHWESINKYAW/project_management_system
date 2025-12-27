<template>
    <div class="task-edit-wrapper pb-3">
        <div class="breadcrumb-and-header pb-6">
            <PageHeader title="Edit Task" :breadcrumbs="breadcrumbs" :show-action-btn="false"
                :permission="'task.create'" />
        </div>
        <div class="task-edit-form-wrapper bg-surface text-on-surface px-5 py-3 box-shadow rounded">
            <form class="task-edit-form grid grid-cols-2 gap-4">
                <div class="task-project py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Project
                    </label>
                    <SelectBox v-model="taskForm.project" :options="projectOptions" />
                </div>
                <div class="task-name py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Task Name
                    </label>
                    <InputBox v-model="taskForm.name" label="Task Name" placeholder="Please type task name"
                        type="text" />
                </div>

                <div class="task-deadline py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Task Deadline
                    </label>

                    <DatePicker v-model="taskForm.dueDate" :enable-time-picker="false" dark />
                </div>

                <div class="task-collaborator py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Collaborator
                    </label>
                    <SelectBox v-model="taskForm.colaborator" multiple :options="colaboratorOptions" />
                </div>

                <div class="task-description col-span-2 py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Task Description
                    </label>
                    <Textarea v-model="taskForm.description" label="Task Description"
                        placeholder="Please type task description" type="text" />
                </div>
            </form>
            <div class="flex justify-end gap-x-4 pt-4">
                <SmallButton name="Update" @click="updateTask" :is-loading="loading.submit"
                    customClasses="bg-primary text-on-primary rounded py-1.5" :icon="IconBuildingStore" />
            </div>
        </div>
        <DotLoading v-if="loading.submit" :show-dots="false" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import api from '@/utils/api';
import { IconBuildingStore, IconLibraryPlusFilled } from '@tabler/icons-vue';
import { useRoute, useRouter } from 'vue-router';
import { toast } from 'vue3-toastify';


const colaboratorOptions = ref([]);
const projectOptions = ref([]);
const router = useRouter();
const route = useRoute();
const taskId = ref(route.params.id);
const initialProjectId = ref(null);

const loading = reactive({
    colaborator: false,
    submit: false,
    detail: false
})

onMounted(() => {
    fetchAllProjects();
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
        name: "Edit Task",
        route: "",
    },
];

const taskForm = reactive({
    name: '',
    description: '',
    dueDate: null,
    colaborator: [],
    project: []
});

watch(() => taskForm.project, (newVal) => {
    if (newVal && newVal.length > 0) {
        let currentProjectId = newVal[0].value;
        // If the project has changed, reset the collaborators
        if (initialProjectId.value !== currentProjectId) {
            taskForm.colaborator = [];
        }
        fetchColaboratorList(currentProjectId);
    }
}, { deep: true })

const fetchTaskDetail = (id) => {
    loading.detail = true;
    api.get(`/task/detail/${id}`).then((res) => {
        let response = res?.data?.data;
        taskForm.description = response.description
        taskForm.name = response.name
        taskForm.dueDate = response.due_date
        taskId.value = response?.id;
        taskForm.colaborator = response.collaborators.map((item) => {
            return {
                value: item.id,
                label: item.name
            }
        })
        taskForm.project = [{
            value: response.project.id,
            label: response.project.name
        }]
        initialProjectId.value = response.project.id;
        loading.detail = false
    }).catch((error) => {
        console.error(error);
    })

}

const fetchAllProjects = () => {
    api.get('/project/list-for-task-store').then((res) => {
        projectOptions.value = res.data.data.map((item) => {
            return {
                value: item.id,
                label: item.name
            }
        });
    }).catch((error) => {
        console.error(error);
    })
}

const fetchColaboratorList = (projectId) => {
    loading.colaborator = true;
    api.get(`/project/colaborator/list/${projectId}`, {
    }).then((res) => {
        colaboratorOptions.value = transformColaborator(res?.data?.data)
        loading.colaborator = false
    }).catch((error) => {
        console.error(error)
    })
}

const transformColaborator = (data) => {
    return data.map((item) => {
        return {
            value: item.id,
            label: item.name,
        }
    })
}

const updateTask = () => {
    loading.submit = true;
    api.post(`/task/update/${taskId.value}`, transformTaskPayload()).then((res) => {
        loading.submit = false;
        router.push({
            name: 'task-list'
        })
    }).catch((error) => {
        loading.submit = false;
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
        project_id: taskForm.project[0]?.value,
        due_date: taskForm.dueDate,
    }
}

</script>
