<script setup>
import SidebarProvider from './layout/SidebarProvider.vue';

</script>

<template>
    <div>
        <sidebar-provider>
            <router-view></router-view>
        </sidebar-provider>
    </div>
</template>
