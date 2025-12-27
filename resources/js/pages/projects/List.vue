<template>
    <div class="project-listing-wrapper">
        <div class="breadcrumb-and-header pb-6">
            <PageHeader title="Projects List" :breadcrumbs="breadcrumbs" @action="headerAction" action-btn-name="Create"
                permission="project.create" :action-btn-icon="IconLibraryPlusFilled" />
        </div>

        <div class="customer-form-wrapper mb-5 bg-surface text-on-surface box-shadow rounded ">
            <div class="grid grid-cols-11 p-3 rounded-md gap-x-2">
                <div class="col-span-3">
                    <InputBox v-model="form.name" placeholder="Project name..." type="text" />
                </div>
                <div class="col-span-3">
                    <!-- <InputBox v-model="form.description" placeholder="Project description..." type="text" /> -->
                    <InputBox v-model="form.creator" placeholder="Creator" type="text" />

                </div>
                <div class="col-span-3 ">
                    <SelectBox v-model="form.status" :options="statusOption" placeholder="Select project status" />

                </div>
                <div class="col-span-2 flex justify-start gap-x-2">
                    <div class="flex justify-center items-center">
                        <SmallButton :name="'search'" :customClasses="'bg-primary text-on-primary rounded py-1.5'"
                            :icon="IconSearch" @click="search" />
                    </div>
                    <div class="flex justify-center items-center">
                        <SmallButton :customClasses="'bg-secondary text-on-secondary rounded py-1.5'"
                            :icon="IconRestore" @click="resetSearch" />
                    </div>
                </div>
            </div>
        </div>
        <FooTable :json="tableJson" :loading="loading" :pagination="pagination" @pageChanged="changePage" />

        <Model :visible="openProjectAddModel" @dismiss="handleModelClose" @submit="handleModelSubmit"
            :submit-loading="submitLoading">
            <form class="project-add-form py-3">
                <div class="project-name py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Project Name
                    </label>
                    <InputBox v-model="projectForm.name" label="Project Name" placeholder="Please type project name"
                        type="text" />
                </div>

                <div class="project-deadline py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Project Deadline
                    </label>

                    <DatePicker v-model="projectForm.dueDate" :enable-time-picker="false" dark />
                </div>

                <div class="project-description py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Project Description
                    </label>
                    <Textarea v-model="projectForm.description" label="Project Description"
                        placeholder="Please type project description" type="text" />
                </div>

                <div class="project-collaborator pt-">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Collaborator
                    </label>
                    <SelectBox v-model="projectForm.colaborator" multiple :options="colaboratorOptions" />
                </div>
            </form>
            <DotLoading v-if="colaboratorLoading" />
        </Model>
    </div>
</template>

<script setup>
import { IconLibraryPlusFilled, IconSearch, IconRestore, IconEdit, IconEye } from '@tabler/icons-vue';
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import tableJson from '@/dummy/project-table-data.json'
import api from '@/utils/api';
import { checkPermissions, formatStatus } from '@/utils/helper.js'

const openProjectAddModel = ref(false);

const loading = ref(true)
const colaboratorLoading = ref(false);
const submitLoading = ref(false);
const detailFetchLoading = ref(false);
const isEditingMode = ref(false);
const colaboratorOptions = ref([]);
const router = useRouter()
const projectId = ref(null);
const statusOption = ref([
    {
        label: "Pending",
        value: "pending",
    },
    {
        label: "In Progress",
        value: "in_progress"
    },
    {
        label: "Complete",
        value: "complete"
    }
])
const pagination = reactive({
    currentPage: 1,
    total: 0,
    totalPages: 0,
    startIndex: 1,
});

const form = reactive({
    name: null,
    description: null,
    creator: null,
    status: [],
})

const projectForm = reactive({
    name: '',
    description: '',
    dueDate: null,
    colaborator: []
})

const breadcrumbs = [
    {
        name: "Home",
        route: "/",
    },
    {
        name: "ProjectList",
        route: "",
    },
];

onMounted(() => {
    fetchProject(1)
})

const search = async () => {
    await fetchProject(pagination.currentPage);
}


const fetchProject = async (page = 1) => {
    console.log(form.status)
    loading.value = true;
    api.get('/project/list', {
        params: {
            page: page,
            per_page: 10,
            status: form.status[0]?.value,
            description: form.description,
            name: form.name,
            creator: form.creator
        }
    }).then((res) => {
        tableJson.body.data = transformData(res.data.data);
        paginationData(res.data.meta);
        loading.value = false
    }).catch((error) => {
        console.error(error)
    })
}

const transformData = (data) => {
    return data.map((item) => {
        return {
            'id': item.id,
            'name': item.name,
            'description': item.description,
            'creator': item.created_by,
            'status': formatStatus(item.status),
            'created_at': item.created_at,
            'actions': actions(item),
        }
    })
}

const actions = (data) => {
    let actions = [];

    if (checkPermissions('project.update')) {
        let edit = {
            'classes': 'bg-warning text-white flex items-center',
            'icon': IconEdit,
            action: () => {
                fetchProjectDetail(data.id);
                fetchColaboratorList()
            }
        }

        actions.push(edit);
    }

    if (checkPermissions('project.detail')) {
        let detail = {
            'classes': 'bg-secondary text-white flex items-center',
            'icon': IconEye,
            action: () => {
                router.push({
                    name: 'project-detail',
                    params: {
                        id: data.id
                    }
                })
            }
        }

        actions.push(detail);
    }

    return actions;
}

const fetchProjectDetail = (id) => {
    detailFetchLoading.value = true;
    api.get(`/project/detail/${id}`).then((res) => {
        let response = res?.data?.data;
        projectForm.description = response.description
        projectForm.name = response.name
        projectForm.dueDate = response.due_date
        projectId.value = response?.id;
        projectForm.colaborator = response.collaborators.map((item) => {
            return {
                value: item.id,
                label: item.name
            }
        })
        isEditingMode.value = true;
        openProjectAddModel.value = true;
        detailFetchLoading.value = false
    }).catch((error) => {
        console.error(error);
    })

}

const fetchColaboratorList = () => {
    colaboratorLoading.value = true;
    api.get('/project/colaborator/list', {
    }).then((res) => {
        colaboratorOptions.value = transformColaborator(res?.data?.data)
        colaboratorLoading.value = false
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


const paginationData = (data) => {
    pagination.currentPage = data.current_page;
    pagination.total = data.total;
    pagination.totalPages = data.last_page;
    pagination.startIndex = data.from;
}

const resetSearch = () => {
    form.name = '';
    form.description = '';
    form.creator = '';
    form.status = []
    fetchProject(1);
}

const reset = async () => {
    projectForm.name = '';
    projectForm.description = '';
    projectForm.dueDate = null;
    projectForm.colaborator = [];
    fetchProject()
}

const headerAction = () => {
    openProjectAddModel.value = true;
    fetchColaboratorList()
}

const handleModelClose = (visible) => {
    openProjectAddModel.value = visible
    projectForm.name = '';
    projectForm.description = '';
    projectForm.dueDate = null;
}

const handleModelSubmit = () => {
    if (isEditingMode.value) {
        updateProject(projectId.value)
    } else {
        storeProject()
    }
}

const storeProject = () => {
    submitLoading.value = true;
    api.post('/project/store', transformProjectPayload()).then((res) => {
        openProjectAddModel.value = false
        reset()
        submitLoading.value = false;
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

const updateProject = (id) => {
    submitLoading.value = true;
    api.post(`/project/update/${id}`, transformProjectPayload()).then((res) => {
        openProjectAddModel.value = false
        reset()
        isEditingMode.value = false;
        submitLoading.value = false;
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

const transformProjectPayload = () => {
    return {
        name: projectForm.name,
        description: projectForm.description,
        collaborators: projectForm.colaborator.map(item => item.value),
        due_date: projectForm.dueDate,
    }
}

const changePage = (page) => {
    if (page !== '...') {
        pagination.currentPage = page;
        fetchProject(page);
    }
}

</script>
