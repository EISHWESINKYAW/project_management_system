<template>
    <div :class="['sidebar-wrapper me-2 relative bg-surface menu-box-shadow text-on-surface min-h-screen transition-all duration-300 ease-in-out', isExpanded || isHovered ? 'expend-sidebar' : 'mini-sidebar']"
        @mouseenter="setIsHovered(true)" @mouseleave="setIsHovered(false)">
        <div
            :class="['sidebar-header fixed bg-surface flex items-center px-2 py-6 shadow-xs', isExpanded || isHovered ? 'expend-sidebar justify-between' : 'mini-sidebar justify-center']">
            <div class="logo-section">
                <span v-if="isExpanded || isHovered" class="logo">
                    Assigner
                </span>
                <span v-else class="icon text-center">
                    A
                </span>
            </div>
            <button :class="['toggler cursor-pointer', isExpanded || isHovered ? '' : 'hidden']" @click="toggleSidebar">
                <IconCircleDot v-if="isExpanded" stroke={2} />
                <IconCircle v-else stroke={2} />
            </button>
        </div>
        <nav class="sidebar-item-wrapper px-2 mt-[75px] overflow-auto">
            <ul class="nav-wrapper flex flex-col gap-y-1.5 pt-3">
                <li v-for="link in navLinks.filter(l => hasAnyPermission(l.permission))" :key="link.key"
                    @click="navigate(link)" :class="['nav-item px-3 py-2 rounded-md cursor-pointer flex items-center gap-x-2',
                        isActive(link.key) ? 'bg-primary text-on-primary' : 'hover:bg-hover text-grey-700']">
                    <component :is="link.icon" class="w-6 h-6" />
                    <span :class="['nav-title transition-all duration-300 overflow-hidden text-nowrap',
                        isExpanded || isHovered ? 'w-full' : 'hidden w-0']">
                        {{ link.name }}
                    </span>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script setup>
import { IconGauge, IconDeviceProjector, IconBrandStackshare, IconCircleDot, IconCircle } from '@tabler/icons-vue';
import { useSidebar } from '@/composables/useSidebar'
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { hasAnyPermission } from '@/utils/helper';

const route = useRoute();
const router = useRouter()
const { isExpanded, isHovered, toggleSidebar, setIsHovered, setActiveItem } = useSidebar()

const routeName = computed(() => route.name);
setActiveItem(routeName.value)

const navLinks = ref([
    {
        name: 'Dashboard',
        to: '/',
        key: 'dashboard',
        icon: IconGauge,
        permission: 'dashboard-view'
    },
    {
        name: 'Projects',
        to: '/project/list',
        key: ['project-list', 'project-detail'],
        icon: IconDeviceProjector,
        permission: 'project'
    },
    {
        name: 'Tasks',
        to: '/task/list',
        key: ['task-list', 'new-task', 'task-detail', 'edit-task'],
        icon: IconBrandStackshare,
        permission: 'task'
    },
    {
        name: 'Role',
        to: '/roles',
        key: ['roles', 'role-create', 'role-edit'],
        icon: IconGauge,
        permission: 'role'
    },
    {
        name: 'User Role',
        to: '/userrolepermission',
        key: ['userrolepermission-list', 'userrolepermission-create', 'userrolepermission-edit'],
        icon: IconDeviceProjector,
        permission: 'user-role-permission'
    },
    {
        name: 'Users',
        to: '/user/list',
        key: ['user-list', 'user-detail'],
        icon: IconBrandStackshare,
        permission: 'user'
    },
])


const isActive = (key) => {
    if (Array.isArray(key)) {
        return key.includes(route.name);
    }
    return route.name === key;
};

const navigate = (data) => {
    router.push(data.to);
}
</script>
