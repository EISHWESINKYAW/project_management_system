<script setup>
import HeaderNav from './HeaderNav.vue';
import Sidebar from './Sidebar.vue';

import { useSidebar } from '@/composables/useSidebar'

const { isExpanded, isHovered } = useSidebar()

</script>


<template>
    <div class="app-container flex">
        <Sidebar />
        <div
            :class="['main-content p-4 flex flex-col bg-background text-on-background gap-y-3 transition-all duration-300 ease-in-out', isExpanded || isHovered ? 'mini-content' : 'expend-content']">
            <HeaderNav />
            <div class="pages-view overflow-auto custom-scrollbar" style="height: calc(100vh - 100px);">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>
