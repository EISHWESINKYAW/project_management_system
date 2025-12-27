<template>
    <div>
        <div class="breadcrumb-and-header pb-6">
            <PageHeader title="Role Create" :breadcrumbs="breadcrumbs" :showActionBtn="false" :permission="''" />
        </div>
        <RoleForm />
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { checkPermissions } from '../../utils/helper';
import RoleForm from '@/components/role/RoleForm.vue';

const breadcrumbs = [
    {
        name: "dashboard",
        route: "/",
    },
    {
        name: "Role List",
        route: "/roles",
    },
    {
        name: "Role Create",
        route: "",
    }
];

</script>
