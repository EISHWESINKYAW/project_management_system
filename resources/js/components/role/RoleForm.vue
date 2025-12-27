<template>
    <div class="role-form-container bg-surface text-on-surface box-shadow rounded px-5">
        <form class="role-add-form py-3">
            <div class="role-name grid grid-cols-7 gap-x-6 py-2">
                <label for="name" class="col-span-1 mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                    Role Name
                </label>
                <div class="col-span-6">
                    <InputBox id="name" v-model="form.name" label="Role Name" placeholder="Please type role name"
                        type="text" />
                </div>
            </div>

            <div class="role-permission grid grid-cols-7 gap-6 py-2">
                <label class="col-span-1 mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                    Permissions
                </label>
                <div class="col-span-6">
                    <PermissionCheckBox :permissions="permissions" @submitPermissions="handleSubmitPermission" />
                </div>
            </div>
            <div class="grid grid-cols-7 pt-5 ">
                <div class="col-span-7 flex justify-end">
                    <SmallButton v-if="!isEdit" :name="'Submit'"
                        customClasses="bg-primary text-on-primary rounded py-1.5" @click="SubmitForm"
                        :isLoading="submitLoading" :icon="IconBuildingStore" />

                    <SmallButton v-else :name="'Update'" customClasses="bg-primary text-on-primary rounded py-1.5"
                        @click="updateForm(route.params.id)" :isLoading="submitLoading" :icon="IconSettingsUp" />
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import PermissionCheckBox from './PermissionCheckBox.vue';
import { IconBuildingStore, IconSettingsUp } from '@tabler/icons-vue';
import api from "../../utils/api";
import { toast } from 'vue3-toastify';
import { useRouter } from 'vue-router';
import { useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

const props = defineProps({
    isEdit: {
        type: Boolean,
        default: false
    }
});

const form = reactive({
    name: '',
    permissions: []
});

const permissions = ref(null);
const submitLoading = ref(false);

const handleSubmitPermission = (permissions) => {
    if (permissions) {
        form.permissions = permissions
    }
}

const fetchRole = async (id) => {
    try {
        const response = await api.get(`/roles/${id}`);
        form.name = response.data.data.name;
        form.permissions = response.data.data.permissions;
        permissions.value = response.data.data.permissions;
    } catch (error) {
        console.error('Error fetching role:', error);
    }
};

const SubmitForm = () => {
    submitLoading.value = true;
    if (form.name === '') {
        alert('Please enter a role name');
        return;
    }
    if (form.permissions === null || form.permissions.length === 0) {
        alert('Please select at least one permission');
        return;
    }
    api.post('/roles', form).then((res) => {
        form.name = '';
        submitLoading.value = false;
        if (res.status == 201) {
            toast.success('Role created successfully!', {
                autoClose: 2000,
                type: 'success',
                onClose: () => {
                    router.push({ name: 'roles' });
                }
            });
        }
    }).catch((error) => {
        submitLoading.value = false;
        if (error) {
            toast.error('Role create failed!', {
                autoClose: 2000,
                type: 'error'
            });
        }
    })
};

const updateForm = (id) => {
    submitLoading.value = true;
    api.post(`/roles/${id}`, form).then((res) => {
        if (res) {
            submitLoading.value = false;
            form.name = '';
            toast.success('Role updated successfully!', {
                autoClose: 2000,
                type: 'success',
                onClose: () => {
                    router.push({ name: 'roles' });
                }
            });
        }
    }).catch((error) => {
        let errors = error?.response?.data?.errors;
        if (errors) {
            submitLoading.value = false;
            Object.values(errors).map((error) => {
                toast.error(error, {
                    autoClose: 2000,
                    type: 'error'
                })
            })
        }
    });
}

onMounted(() => {
    if (props.isEdit) {
        const roleId = route.params.id;
        fetchRole(roleId);
    }
});

</script>
