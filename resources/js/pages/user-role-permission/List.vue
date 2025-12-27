<template>
    <div>
        <div class="breadcrumb-and-header pb-6">
            <PageHeader title="Role List" :breadcrumbs="breadcrumbs" @action="headerAction"
                action-btn-name="Add User Role Permission" :permission="''" />
        </div>
        <FooTable :json="tableJson" :loading="loading" :pagination="pagination" @pageChanged="changePage" />
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import tableJson from '@/dummy/user-role-permissions-table-data.json';
import api from '@/utils/api';
import { IconEdit } from '@tabler/icons-vue';

const breadcrumbs = [
    {
        name: "Dashboard",
        route: "/",
    },
    {
        name: "User Role Permissions",
        route: "",
    },
];

const loading = ref(true);
const router = useRouter();

const pagination = reactive({
    currentPage: 1,
    total: 0,
    totalPages: 0,
    startIndex: 1,
});

const headerAction = () => {
    router.push({
        name: 'userrolepermission-create'
    })
}

onMounted(() => {
    fetchUsers(1)
})

const fetchUsers = async (page = 1) => {
    loading.value = true;
    await api.get('/user-role/list',{
        params: {
            page: page,
            per_page: 10
        }
    }).then((res) => {
        tableJson.body.data = transformData(res.data.data);
        paginationData(res.data.meta);
        loading.value = false
    }).catch((error) => {
        console.error(error)
        loading.value = false
    })
}

const transformData = (data) => {
    return data.map((item) => {
        return {
            'id': item.id,
            'user': item.name,
            'role': item.role.name,
            'created_at': item.created_at,
            'actions': actions(item),
        }
    })
}

const changePage = (page) => {
    pagination.currentPage = page;
    fetchUsers(page);
}

const paginationData = (data) => {
    pagination.currentPage = data.current_page;
    pagination.total = data.total;
    pagination.totalPages = data.last_page;
    pagination.startIndex = data.from;
}

const actions = (data) => {
    let actions = [];

    if (true) {
        let edit = {
            'classes': 'bg-yellow-500 text-white flex items-center',
            'icon': IconEdit,
            action: () => {
                router.push({
                    name: 'userrolepermission-edit',
                    params: { id: data.id }
                });
            }
            }

        actions.push(edit);
    }

    return actions;
}

</script>
