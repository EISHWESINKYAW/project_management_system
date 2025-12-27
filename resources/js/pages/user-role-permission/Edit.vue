<template>
    <div>
        <div class="breadcrumb-and-header pb-6">
            <PageHeader title="User Role Create" :breadcrumbs="breadcrumbs" :showActionBtn="false" :permission="''" />
        </div>
        <UserRolePermissionForm :isEdit="true" />
    </div>
</template>

<script setup>
import UserRolePermissionForm from '@/components/role/UserRolePermissionForm.vue';
const breadcrumbs = [
    {
        name: "Dashboard",
        route: "/",
    },
    {
        name: "User Role Permissions",
        route: "/userrolepermission",
    },
    {
        name: "User Role Permissions Edit",
        route: "",
    }
];
</script>
