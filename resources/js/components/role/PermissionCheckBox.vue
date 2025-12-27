<template>
    <div class="overflow-hidden rounded border border-border">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full bg-surface">
                <thead>
                    <tr class="border-b-4 border-border bg-surface">
                        <th class="px-5 py-3 text-left w-3/11 sm:px-6">
                            <p class="text-base text-on-surface font-bold">Module</p>
                        </th>
                        <th class="px-5 py-3 text-left w-1/11 sm:px-6">
                            <p class="font-medium text-base text-on-surface">CheckAll</p>
                        </th>
                        <th class="px-5 py-3 text-left w-1/11 sm:px-6">
                            <p class="font-medium text-base text-on-surface">Create</p>
                        </th>
                        <th class="px-5 py-3 text-left w-1/11 sm:px-6">
                            <p class="font-medium text-base text-on-surface">Detail</p>
                        </th>
                        <th class="px-5 py-3 text-left w-1/11 sm:px-6">
                            <p class="font-medium text-base text-on-surface">Edit</p>
                        </th>
                        <th class="px-5 py-3 text-left w-1/11 sm:px-6">
                            <p class="font-medium text-base text-on-surface">Update</p>
                        </th>
                        <th class="px-5 py-3 text-left w-1/11 sm:px-6">
                            <p class="font-medium text-base text-on-surface">Delete</p>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 ">
                    <tr v-if="loading" class="border-t border-border">
                        <td colspan="6" class=" px-3 py-4 text-gray-500">
                            <div class="flex justify-center">
                                <SpinLoading />
                            </div>
                        </td>
                    </tr>
                    <tr v-else v-for="(permission, index) in permissions" :key="index" class="border-t border-border ">
                        <td class="px-5 py-4 sm:px-6 text-on-surface">
                            {{ permission.name }}
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <CheckBox v-model="permissionVal[permission.id].all" :id="`${permission.slug}-all`"
                                :disabled="permission.create || permission.read || permission.update || permission.delete"
                                @update="checkAllPermissions(permission.id)" />
                        </td>
                        <td class=" px-5 py-4 sm:px-6">
                            <CheckBox v-model="permissionVal[permission.id].create" :id="`${permission.slug}-create`"
                                :disabled="permission.create" @update="updateAllCheckbox(permission.id)" />
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <CheckBox v-model="permissionVal[permission.id].detail" :id="`${permission.slug}-detail`"
                                :disabled="permission.detail" @update="updateAllCheckbox(permission.id)" />
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <CheckBox v-model="permissionVal[permission.id].edit" :id="`${permission.slug}-edit`"
                                :disabled="permission.edit" @update="updateAllCheckbox(permission.id)" />
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <CheckBox v-model="permissionVal[permission.id].update" :id="`${permission.slug}-update`"
                                :disabled="permission.update" @update="updateAllCheckbox(permission.id)" />
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <CheckBox v-model="permissionVal[permission.id].delete" :id="`${permission.slug}-delete`"
                                :disabled="permission.delete" @update="updateAllCheckbox(permission.id)" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from "vue";
import api from "../../utils/api";

const props = defineProps({
    permissions: {
        type: Object,
        default: () => null
    }
})
const emit = defineEmits(["submitPermissions"]);

const permissionVal = ref({});
const permissions = ref(props.permissions);
const isBulkUpdating = ref(false);
const loading = ref(true);

onMounted(async () => {
    await fetchPermission();
    if (props.permissions) {
        updatePermissions(props.permissions)
    }
})

watch(() => props.permissions, (newPermission) => {
    isBulkUpdating.value = true;
    loading.value = true;
    if (newPermission) {
        newPermission.forEach(p => {
            permissionVal.value[p.id] = {
                create: p.create,
                detail: p.detail,
                update: p.update,
                edit: p.edit,
                delete: p.delete,
                all: p.create && p.detail && p.update && p.edit && p.delete,
            };
        });
    }
    isBulkUpdating.value = false;
    loading.value = false;
})


watch(
    permissionVal,
    () => {
        if (!isBulkUpdating.value) {
            emit("submitPermissions", permissionVal.value);
        }
    },
    { deep: true }
);

const updatePermissions = (permissions) => {
    isBulkUpdating.value = true;
    loading.value = true;
    if (permissions) {
        permissions.forEach(p => {
            permissionVal.value[p.id] = {
                create: p.create,
                detail: p.detail,
                update: p.update,
                edit: p.edit,
                delete: p.delete,
                all: p.create && p.detail && p.update && p.edit && p.delete,
            };
        });
    }
    isBulkUpdating.value = false;
    loading.value = false;
}

const fetchPermission = async () => {
    isBulkUpdating.value = true;
    loading.value = true;
    await api.get('/permissions').then((res) => {
        permissions.value = res?.data?.data;
        permissions.value.forEach(p => {
            permissionVal.value[p.id] = {
                create: false,
                detail: false,
                update: false,
                delete: false,
                edit: false,
                all: false,
            };
        });
        isBulkUpdating.value = false;
        loading.value = false;
    }).catch((error) => {
        console.error(error);
    });
}

const checkAllPermissions = (id) => {
    const isChecked = permissionVal.value[id].all;
    isBulkUpdating.value = true;

    permissionVal.value[id].create = isChecked;
    permissionVal.value[id].detail = isChecked;
    permissionVal.value[id].update = isChecked;
    permissionVal.value[id].edit = isChecked;
    permissionVal.value[id].delete = isChecked;

    // Give Vue one tick to finish updates, then re-enable emit
    queueMicrotask(() => {
        isBulkUpdating.value = false;
    });
};

const updateAllCheckbox = (id) => {
    isBulkUpdating.value = true;

    const perms = permissionVal.value[id];
    perms.all = perms.create && perms.detail && perms.update && perms.edit && perms.delete;

    queueMicrotask(() => {
        isBulkUpdating.value = false;
    });
};

</script>

<style scoped></style>
