<template>
    <div>
        <div class="breadcrumb-and-header pb-6">
            <PageHeader title="Role List" :breadcrumbs="breadcrumbs" @action="headerAction" action-btn-name="Add Role"
                :permission="'role.create'" />
        </div>
        <div class="customer-form-wrapper mb-5 bg-surface text-on-surface box-shadow rounded ">
            <div class="grid grid-cols-11 p-3 rounded-md gap-x-2">
                <div class="col-span-3">
                    <InputBox v-model="form.name" placeholder="Role" type="text" />
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
import { useRouter } from 'vue-router';
import { checkPermissions } from '../../utils/helper';
import { ref, reactive, onMounted } from 'vue';
import { IconSearch, IconRestore, IconEdit, IconTrash, IconEye } from '@tabler/icons-vue';
import tableJson from '@/dummy/role-table-data.json';
import api from '@/utils/api';
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';

const router = useRouter();
const headerAction = () => {
    router.push({
        name: 'role-create'
    })
}

const breadcrumbs = [
    {
        name: "Dashboard",
        route: "/",
    },
    {
        name: "Roles",
        route: "",
    },
];

const form = reactive({
    name: '',
});

const loading = ref(false);
const pagination = reactive({
    currentPage: 1,
    total: 0,
    totalPages: 0,
    startIndex: 1,
});

const fetchRoles = async (page = 1) => {
    loading.value = true;
    api.get('/roles', {
        params: {
            page: page,
            per_page: 10,
            ...form
        }
    }).then((res) => {
        tableJson.body.data = transformData(res.data.data);
        paginationData(res.data.meta);
        loading.value = false
    }).catch((error) => {
        console.error(error)
    })
}

const search = async () => {
    await fetchRoles(pagination.currentPage);
}

const transformData = (data) => {
    return data.map((item) => {
        return {
            'id': item.id,
            'name': item.name,
            'slug': item.slug,
            'created_at': item.created_at,
            'actions': actions(item),
        }
    })
}

const paginationData = (data) => {
    pagination.currentPage = data.current_page;
    pagination.total = data.total;
    pagination.totalPages = data.last_page;
    pagination.startIndex = data.from;
}

const actions = (data) => {
    let actions = [];

    if (checkPermissions('role.update')) {
        let edit = {
            'classes': 'bg-warning text-white flex items-center',
            'icon': IconEdit,
            action: () => {
                router.push({
                    name: 'role-edit',
                    params: {
                        id: data.id
                    }
                })
            }
        }

        actions.push(edit);
    }

    if (checkPermissions('role.delete')) {
        let remove = {
            'classes': 'bg-red-500 text-white flex items-center',
            'icon': IconTrash,
            action: () => {
                Swal.fire({
                    title: "Are you sure?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Delete",
                    background: "#192841",
                    backdrop: `
                        rgba(0,0,0,0.6)
                    `
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log('Delete action triggered for role ID:', data.id);
                        loading.value = true;
                        api.delete(`/roles/${data.id}`)
                            .then((res) => {
                                if (res) {
                                    toast.success('Role deleted successfully!', {
                                        autoClose: 2000,
                                        type: 'success',
                                    });
                                    loading.value = false;
                                }

                                fetchRoles();
                            })
                            .catch((error) => {
                                let errors = error?.response?.data?.errors;
                                if (errors) {
                                    Object.values(errors).map((error) => {
                                        toast.error(error, {
                                            autoClose: 2000,
                                            type: 'error'
                                        })
                                    })
                                    loading.value = false;
                                }
                            });
                    }
                });
            }
        }

        actions.push(remove);
    }

    return actions;
}

const reset = () => {
    form.name = '';
    fetchRoles(1);
}

const changePage = (page) => {
    pagination.currentPage = page;
    fetchRoles(page);
}

onMounted(() => {
    fetchRoles(1);
});

</script>
