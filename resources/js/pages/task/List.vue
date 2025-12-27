<template>
    <div class="task-listing-wrapper">
        <div class="breadcrumb-and-header pb-6">
            <PageHeader title="Tasks List" :breadcrumbs="breadcrumbs" @action="headerAction" action-btn-name="Create"
                permission="task.create" :action-btn-icon="IconLibraryPlusFilled" />
        </div>

        <div class="task-form-wrapper mb-5 bg-surface text-on-surface box-shadow rounded ">
            <div class="grid grid-cols-11 p-3 rounded-md gap-x-2">
                <div class="col-span-3">
                    <InputBox v-model="form.name" placeholder="Task name..." type="text" />
                </div>
                <div class="col-span-3">
                    <InputBox v-model="form.description" placeholder="Task description..." type="text" />
                </div>
                <div class="col-span-3 ">
                    <!-- <InputBox v-model="form.creator" placeholder="Creator" type="text" /> -->
                    <SelectBox v-model="form.status" :options="statusOption" placeholder="Select task status" />
                </div>
                <div class="col-span-2 flex justify-start gap-x-2">
                    <div class="flex justify-center items-center">
                        <SmallButton :name="'search'" :customClasses="'bg-primary text-on-primary rounded py-1.5'"
                            :icon="IconSearch" @click="search" />
                    </div>
                    <div class="flex justify-center items-center">
                        <SmallButton :customClasses="'bg-secondary text-on-secondary rounded py-1.5'"
                            :icon="IconRestore" @click="reset" />
                    </div>
                </div>
            </div>
        </div>
        <FooTable :json="tableJson" :loading="loading" :pagination="pagination" @pageChanged="changePage" />
    </div>
</template>

<script setup>
import { IconLibraryPlusFilled, IconSearch, IconRestore, IconEdit, IconEye } from '@tabler/icons-vue';
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import tableJson from '@/dummy/task-table-data.json'
import api from '@/utils/api';
import { checkPermissions } from '@/utils/helper.js'

const loading = ref(true)
const router = useRouter()

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
    status: []

})

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
        label: "Completed",
        value: "completed"
    }
])

const breadcrumbs = [
    {
        name: "Home",
        route: "/",
    },
    {
        name: "TaskList",
        route: "",
    },
];

onMounted(() => {
    fetchTask(1)
})

const search = async () => {
    await fetchTask(pagination.currentPage);
}


const fetchTask = async (page = 1) => {
    loading.value = true;
    api.get('/task/list', {
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
            'project': item.project.name,
            'name': item.name,
            'creator': item.created_by,
            'due_date': item.due_date,
            'status': item.status,
            'actions': actions(item),
        }
    })
}

const actions = (data) => {
    let actions = [];

    if (checkPermissions('task.update')) {
        let edit = {
            'classes': 'bg-warning text-white flex items-center',
            'icon': IconEdit,
            action: () => {
                router.push({
                    name: 'edit-task',
                    params: {
                        id: data.id
                    }
                })
            }
        }

        actions.push(edit);
    }

    let detail = {
        'classes': 'bg-secondary text-white flex items-center',
        'icon': IconEye,
        action: () => {
            router.push({
                name: 'task-detail',
                params: {
                    id: data.id
                }
            })
        }
    }

    actions.push(detail);

    return actions;
}


const paginationData = (data) => {
    pagination.currentPage = data.current_page;
    pagination.total = data.total;
    pagination.totalPages = data.last_page;
    pagination.startIndex = data.from;
}

const reset = async () => {
    form.name = '';
    form.description = '';
    form.creator = '';
    await fetchTask(1);
}

const headerAction = () => {
    router.push({
        name: 'new-task'
    });
}

const changePage = (page) => {
    if (page !== '...') {
        pagination.currentPage = page;
        fetchTask(page);
    }
}

</script>
