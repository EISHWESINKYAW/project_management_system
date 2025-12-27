<template>
    <div class="task-add-wrapper pb-3">
        <div class="breadcrumb-and-header pb-6">
            <PageHeader title="New Task" :breadcrumbs="breadcrumbs" :show-action-btn="false"
                :permission="'task.create'" />
        </div>
        <div class="task-add-form-wrapper bg-surface text-on-surface px-5 py-3 box-shadow rounded">
            <form class="task-add-form grid grid-cols-2 gap-4">
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
                <SmallButton name="Create" @click="handleStore" :is-loading="loading.submit"
                    customClasses="bg-primary text-on-primary rounded py-1.5" :icon="IconBuildingStore" />
            </div>
        </div>
        <DotLoading v-if="loading.submit" :show-dots="false" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import api from '@/utils/api';
import { IconBuildingStore } from '@tabler/icons-vue';
import { useRouter } from 'vue-router';
import { toast } from 'vue3-toastify';


const colaboratorOptions = ref([]);
const projectOptions = ref([]);
const router = useRouter();

const loading = reactive({
    colaborator: false,
    submit: false,
})

onMounted(() => {
    fetchAllProjects();
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
        name: "New Task",
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

watch(taskForm.project, (newVal) => {
    console.log('Project changed:', newVal);
    if (newVal && newVal.length > 0) {
        fetchColaboratorList(newVal[0].value);
    }
}, { immediate: true })

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

const handleStore = () => {
    loading.submit = true;
    api.post('/task/store', transformTaskPayload()).then((res) => {
        loading.submit = false;
        router.push({
            name: 'task-list'
        })
    }).catch((error) => {
        let errors = error?.response?.data?.errors;
        if (errors) {
            loading.submit = false;
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
