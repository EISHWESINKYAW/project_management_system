<template>
    <div>
        <div class="breadcrumb-and-header pb-6">
            <PageHeader title="Role Edit" :breadcrumbs="breadcrumbs" :showActionBtn="false" />
        </div>
        <RoleForm :isEdit="true" />
    </div>
</template>

<script setup>
import RoleForm from '@/components/role/RoleForm.vue';

const breadcrumbs = [
    {
        name: "Dashboard",
        route: "/",
    },
    {
        name: "Role List",
        route: "/roles",
    },
    {
        name: "Edit Role",
        route: "",
    }
];
</script>
