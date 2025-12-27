<template>
    <div
        class="app-navbar sticky z-30 select-none bg-surface text-on-surface box-shadow py-2 px-3 rounded-md flex justify-between items-center">
        <div class="left-side w-1/3">
            <!-- <InputBox v-model="search" /> -->
        </div>
        <div class="right-side flex items-center gap-x-3">
            <div class="theme-toggler cursor-pointer hover:bg-hover w-10 h-10 rounded-full flex items-center justify-center"
                @click="toggleDark()">
                <div class="icon-wrapper">
                    <IconMoon v-if="isDark" class="w-7 h-7" />
                    <IconSun v-else class="w-7 h-7" />
                </div>
            </div>
            <NotificationMenu />
            <UserMenu />
        </div>
    </div>
</template>

<script setup>

import { ref } from 'vue';
import InputBox from '@/components/common/InputBox.vue';
import UserMenu from './UserMenu.vue';
import { IconSun, IconMoon } from '@tabler/icons-vue';
import { useDark, useToggle } from '@vueuse/core'
import NotificationMenu from './NotificationMenu.vue';
import websocket from '@/utils/websocket';

const isDark = useDark({
    selector: 'body',
    attribute: 'data-theme',
    valueDark: 'dark',
})
const toggleDark = useToggle(isDark)
const authToken = localStorage.getItem('token');
if (authToken) {
    console.log('WebSocketService initialized with auth token:', websocket);
    websocket.init(authToken);
}

const search = ref(null);

</script>
