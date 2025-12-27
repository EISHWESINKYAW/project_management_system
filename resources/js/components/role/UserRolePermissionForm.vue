<template>
    <div>
        <div class="grid grid-cols-4 gap-x-5 py-2.5 my-3 bg-surface rounded">
            <div class="col-span-2 rounded-md px-3 py-0.5">
                <div class="user-name flex flex-col gap-y-2 py-2">
                    <label class="text-sm text-nowrap">User</label>
                    <SelectBox v-model="form.user" :options="users" />
                </div>

            </div>
            <div class="col-span-2 rounded-md px-3 py-0.5">
                <div class="user-email flex flex-col gap-y-2 py-2">
                    <label class="text-sm text-nowrap">Role</label>
                    <SelectBox v-model="form.role" :options="roles" />
                </div>
            </div>
        </div>
        <div class=" pt-2.5 bg-surface px-4 pb-2 rounded">
            <div class="permissions flex flex-col gap-y-2">
                <label class="text-sm text-nowrap">Permissions</label>
                <PermissionCheckBox :permissions="permissions" @submitPermissions="handleSubmitPermission" />
            </div>
            <div class="form-action flex justify-end m-4">
                <SmallButton v-if="!isEdit" :name="'Submit'" :customClasses="'bg-primary text-on-primary rounded'"
                    @click="handleSubmit" :isLoading="loading" />
                <SmallButton v-else :name="'Update'" :customClasses="'bg-primary text-on-primary rounded'"
                    @click="updateForm" :isLoading="submitLoading" />
            </div>
        </div>
    </div>
    <DotLoading v-if="loading" :show-dots="showLoadingDots" />

    <!-- role ပြောင်းလိုက်ရင် ပေါလာအောင်အတွက်... -->
    <DotLoading v-if="permissionLoading" />
</template>

<script setup>
import { reactive, ref, onMounted, watch } from 'vue';
import api from "../../utils/api";
import PermissionCheckBox from "./PermissionCheckBox.vue";
import { toast } from 'vue3-toastify';
import { useRoute, useRouter } from 'vue-router';

const props = defineProps({
    isEdit: {
        type: Boolean,
        default: false
    }
});

const form = reactive({
    role: [],
    user: [],
    permissions: null
})
const route = useRoute();
const router = useRouter();
const currentRoleId = ref(null);
const loading = ref(false);
const showLoadingDots = ref(false);
const submitLoading = ref(false);
const users = ref([]);
const roles = ref([]);
const permissions = ref(null);
const permissionLoading = ref(false);
const userId = ref(null);

onMounted(() => {
    fetchUsers();
    fetchRoles();
    if (props.isEdit) {
        userId.value = route.params.id;
        fetchRolePermissionDetail();
    }
})

watch(() => form.role, (newRole) => {
    if (newRole) {
        currentRoleId.value = newRole[0].value
        fetchRolePermission(currentRoleId.value)
    }
}, { deep: true })

const fetchUsers = async () => {
    loading.value = true;
    api.get('/users/list').then((res) => {
        users.value = res?.data?.data.map((user) => {
            return {
                value: user.id,
                label: user.name
            }
        })
        loading.value = false;
    }).catch((error) => {
        console.error(error)
    })
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

const fetchRolePermission = (id) => {
    permissionLoading.value = true
    api.get('/roles/permissions', {
        params: {
            role_id: id
        }
    }).then((res) => {
        permissions.value = res?.data.data;
        permissionLoading.value = false;
    }).catch((error) => {
        console.error(error);
    });
}

const handleSubmitPermission = (permissions) => {
    if (permissions) {
        form.permissions = permissions
    }
}

const handleSubmit = () => {
    loading.value = true;
    api.post('/user-role/permission', transformPayload(form)).then((res) => {
        if (res) {
            router.push('/userrolepermission');
        }
    }).catch((error) => {
        loading.value = false;
        let errors = error?.response?.data?.message;
        if (errors) {
            toast.error(errors, {
                autoClose: 2000,
                type: 'error'
            })
        }
    })
}

const transformPayload = (payload) => {
    return {
        'role_id': payload?.role[0]?.value,
        'user_id': payload?.user[0]?.value,
        'permissions': payload?.permissions
    }
}

const fetchRolePermissionDetail = async () => {
    loading.value = true;
    await api.get(`/user-role/permission/${userId.value}`).then((res) => {
        let response = res?.data?.data;
        if (response) {
            currentRoleId.value = response.role.id;
            form.role.push({
                value: response.role.id,
                label: response.role.name
            })
            form.user.push({
                value: response.user.id,
                label: response.user.name
            })
            permissions.value = response.permissions;
        }
        loading.value = false
    }).catch((error) => {
        console.error(error)
    })
}

const updateForm = async () => {
    loading.value = true;
    await api.post(`/user-role/permission/${userId.value}`, transformPayload(form)).then((res) => {
        console.log('user role permission res here', res)
        if (res?.status == '200') {
            router.push('/userrolepermission')
        }
    }).catch((error) => {
        loading.value = false;
        let errors = error?.response?.data?.errors;
        if (errors) {
            let errors = error?.response?.data?.message;
            if (errors) {
                toast.error(errors, {
                    autoClose: 2000,
                    type: 'error'
                })
            }
        }
    })
}

</script>
