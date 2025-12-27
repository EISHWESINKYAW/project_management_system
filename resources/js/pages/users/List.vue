<template>
    <div>
        <div class="breadcrumb-and-header pb-6">
            <PageHeader title="User List" :breadcrumbs="breadcrumbs" @action="headerAction" action-btn-name="Create"
                permission="project.create" :action-btn-icon="IconLibraryPlusFilled" />
        </div>
        <div class="customer-form-wrapper mb-5 bg-surface text-on-surface box-shadow rounded ">
            <div class="grid grid-cols-11 p-3 rounded-md gap-x-2">
                <div class="col-span-3">
                    <InputBox v-model="form.name" placeholder="User name..." type="text" />
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

        <UserCreateModel :visible="openUserAddModel" @dismiss="handleModelClose" @submit="handleModelSubmit">
            <form class="user-add-form py-3">
                <div class="user-name py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        User Name
                    </label>
                    <InputBox v-model="userForm.name" label="User Name" placeholder="Please type user name"
                        type="text" />
                </div>
                <div class="user-email py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Email
                    </label>
                    <InputBox v-model="userForm.email" label="Email" placeholder="Please type email" type="email" />
                </div>
                <div v-if="!isEditingModel" class="user-password py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Password
                    </label>
                    <InputBox v-model="userForm.password" label="Password" placeholder="Please type password"
                        type="password" />
                </div>
                <div class="user-phone py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Phone No
                    </label>
                    <InputBox v-model="userForm.phone" label="Phone" placeholder="Please type phone no" type="phone" />
                </div>
                <div class="user-adddress py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Address
                    </label>
                    <InputBox v-model="userForm.address" label="Address" placeholder="Please type address"
                        type="text" />
                </div>
                <div class="user-gender py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Gender
                    </label>
                    <InputBox v-model="userForm.gender" label="Gender" placeholder="Please type gender" type="text" />
                </div>
                <div class="user-education py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Education
                    </label>
                    <InputBox v-model="userForm.education" label="Education" placeholder="Please type education"
                        type="text" />
                </div>
                <div class="user-nrc py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        NRC No
                    </label>
                    <InputBox v-model="userForm.nrc" label="NRC No" placeholder="Please type NRC No" type="text" />
                </div>
                <div class="user-nrc py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        User Role
                    </label>
                    <SelectBox v-model="userForm.role" placeholder="Select user role..." :options="roles" />
                </div>
                <div class="user-profile py-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                        Profile
                    </label>
                    <div class="flex justify-center items-center text-xs border border-white overflow-hidden">
                        <FileUpload v-model="userForm.profile" label="Profile" class="w-full h-full"
                            placeholder="Please upload profile image" type="file" />

                    </div>
                </div>
            </form>
            <DotLoading v-if="colaboratorLoading" />
        </UserCreateModel>
    </div>
</template>

<script setup>
import PageHeader from '@/components/common/PageHeader.vue';
import { IconLibraryPlusFilled, IconSearch, IconRestore, IconEdit, IconEye } from '@tabler/icons-vue';
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import tableJson from '@/dummy/user-table-data.json'
import api from '@/utils/api';
import UserCreateModel from '../../components/UserCreateModel.vue';
import { toast } from 'vue3-toastify';


const openUserAddModel = ref(false);

const loading = ref(true)
const colaboratorLoading = ref(false);
const isEditingModel = ref(false);
const router = useRouter();
const submitLoading = ref(false);
const roles = ref([]);

const pagination = reactive({
    currentPage: 1,
    total: 0,
    totalPages: 0,
    startIndex: 1,
});

const form = reactive({
    name: '',
})

const userForm = reactive({
    name: '',
    email: '',
    role: '',
    profile: '',
    gender: '',
    education: '',
    address: '',
    password: '',
    nrc: '',
    phone: '',
    role: []
});

const breadcrumbs = [
    {
        name: "Home",
        route: "/",
    },
    {
        name: "UserList",
        route: "",
    },
];


const search = async () => {
    await fetchUsers(pagination.currentPage);
}
const resetSearch = () => {
    form.name = '';
    fetchUsers(1);
}
const reset = async (fetch = true) => {
    userForm.name = '';
    userForm.email = '';
    userForm.password = '';
    userForm.profile = '';
    userForm.phone = '';
    userForm.address = '';
    userForm.nrc = '';
    userForm.education = '';
    userForm.gender = '';
    userForm.role = []

    if (fetch) {
        fetchUsers(1);
    }

}

const headerAction = () => {
    openUserAddModel.value = true;
}
const handleModelClose = (visible) => {
    openUserAddModel.value = visible;
    reset(false);
}

const fetchRoles = async () => {
    loading.value = true;
    api.get('/roles').then((res) => {
        roles.value = res?.data?.data?.map((role) => {
            return {
                value: role.id,
                label: role.name
            }
        })
        loading.value = false
    }).catch((error) => {
        console.error(error)
    })
}

const fetchUsers = async (page = 1) => {
    console.log("fetchUsers called with page:", page);
    loading.value = true;
    api.get('/users/list', {
        params: {
            page: page,
            per_page: pagination.per_page,
            name: form.name
        }
    }).then((response) => {
        console.log("response==>", response);
        const userData = Array.isArray(response.data.data) ? response.data.data : [];
        tableJson.body.data = transformData(userData);
        console.log("tableJson==>", tableJson.body.data);
        paginationData(response.data.meta);
        loading.value = false;
    }).catch((error) => {
        loading.value = false;
        console.error(error)
    })
}
const transformData = (data) => {
    return data.map((item) => {
        return {
            'id': item.id,
            'name': item.name,
            'email': item.email,
            'profile_url': item.profile_url ? item.profile_url : '-',
            'password': item.password,
            'role': item.role ? item.role.name : '-',
            'education': item.education ? item.education : '-',
            'address': item.address ? item.address : '-',
            'phone': item.phone ? item.phone : '-',
            'actions': actions(item),
        }
    })
}
const actions = (data) => {
    let actions = [];

    if (true) {
        let edit = {
            'classes': 'bg-warning text-white flex items-center',
            'icon': IconEdit,
            action: () => {
                fetchUserDetail(data.id);
            }
        }

        actions.push(edit);
    }
    let remove = {
        'classes': 'bg-secondary text-white flex items-center',
        'icon': IconEye,
        action: () => {
            router.push({
                name: 'user-detail',
                params: {
                    id: data.id,
                }
            })
        }
    }

    actions.push(remove);

    return actions;
}

// for user detail
const userId = ref(null);

const fetchUserDetail = async (id) => {
    try {
        colaboratorLoading.value = true;
        const response = await api.get(`/users/detail/${id}`);
        const user = response.data.data;
        console.log("here is user", response)
        userId.value = user.id;
        userForm.name = user.name || '';
        userForm.email = user.email || '';
        userForm.profile = user.profile_url || '';
        // userForm.password = user.password;
        userForm.address = user.address || '';
        userForm.phone = user.phone || '';
        userForm.gender = user.gender || '';
        userForm.nrc = user.nrc_no || '';
        userForm.education = user.education || '';
        userForm.role.push({
            value: user.role.id,
            label: user.role.name
        })
        isEditingModel.value = true;
        openUserAddModel.value = true;
    } catch (error) {
        console.error('Failed to fetch user detail:', error);
    } finally {
        colaboratorLoading.value = false;
    }
};

const changePage = (page) => {
    if (page !== '...') {
        pagination.currentPage = page;
        fetchUsers(page);
    }
}
const paginationData = (data) => {
    pagination.currentPage = data.currentPage;
    pagination.total = data.total;
    pagination.totalPages = data.last_page;
    pagination.startIndex = data.from;
}

const handleModelSubmit = () => {
    if (isEditingModel.value) {
        updateUser(userId.value)
    } else {
        createUser()
    }
}

// user create
const createUser = async () => {
    console.log('Profile value:', userForm.profile);
    submitLoading.value = true;

    api.postWithFile('/users/store', transformUserPayload())
        .then((res) => {
            console.log("user create::", res);
            openUserAddModel.value = false;
            reset();
            submitLoading.value = false;
            toast('User created successfully', {
                autoClose: 2000,
                type: 'success'
            }).catch((err) => {
                console.log(err);
                toast('User create failed', {
                    autoClose: 2000,
                    type: 'error'
                });
            });
        });
}

const updateUser = async (id) => {
    api.postWithFile(`/users/update/${id}`, transformUserPayload()).then((res) => {
        openUserAddModel.value = false
        reset()
    }).catch((err) => {
        console.log(err);
        toast('User update failed', {
            autoClose: 2000,
            type: 'error'
        });
    });
}
const transformUserPayload = () => {
    const payload = {
        name: userForm.name,
        email: userForm.email,
        password: userForm.password,
        profile: userForm.profile ? userForm.profile[0] : null,
        address: userForm.address,
        phone: userForm.phone,
        gender: userForm.gender,
        nrc_no: userForm.nrc,
        education: userForm.education,
        role_id: userForm.role[0].value,
    }
    return payload;
};


onMounted(() => {
    fetchUsers();
    fetchRoles();
});
</script>
