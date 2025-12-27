<template>
    <div class="text-right">
        <Menu as="div" class="relative inline-block text-left">
            <div>
                <MenuButton class="cursor-pointer">
                    <div class="user-avatar w-10 h-10 rounded-full overflow-hidden">
                        <img :src="authUser?.profile_url ? authUser.profile_url : 'https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/avatars/1.png'"
                            alt="" class="w-full h-full object-cover" />
                    </div>
                </MenuButton>
            </div>

            <transition enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in" leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0">
                <MenuItems
                    class="absolute z-20 right-0 mt-6 w-56 origin-top-right rounded-md bg-surface text-on-surface shadow-lg ring-1 ring-gray-200/5 focus:outline-none">
                    <div class="flex flex-col gap-y-2 py-2">
                        <MenuItem>
                        <div class="px-2">
                            <div
                                class="profile-section group flex gap-x-2 w-full items-center rounded-md px-2 py-2 text-sm hover:bg-hover cursor-pointer">
                                <div class="user-avatar w-10 h-10 rounded-full overflow-hidden">
                                    <img :src="authUser?.profile_url ? authUser.profile_url : 'https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/avatars/1.png'"
                                        alt="" class="w-full h-full object-cover"/>
                                </div>
                                <div class="info">
                                    <h1 class="text-base font-medium">{{ authUser?.name }}</h1>
                                    <p class="text-sm">{{ authUser?.role?.name }}</p>
                                </div>
                            </div>
                        </div>
                        </MenuItem>
                        <div class="divider border-b border-border"></div>
                        <MenuItem>
                        <div class="px-2">
                            <SmallButton @click="goToProfile" type="submit" name="Profile"
                                custom-classes="group flex gap-x-2 w-full items-center rounded-md py-2 hover:bg-hover cursor-pointer"
                                :icon="IconUser" />
                        </div>
                        </MenuItem>
                        <MenuItem>
                        <div class="px-2">
                            <SmallButton @click="logout" type="submit" name="Logout" :is-loading="loading"
                                custom-classes="group flex gap-x-2 w-full items-center rounded-md py-2 hover:bg-hover cursor-pointer"
                                :icon="IconLogout2" />
                        </div>
                        </MenuItem>
                    </div>
                </MenuItems>
            </transition>
        </Menu>
    </div>
</template>

<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import { IconUser, IconLogout2 } from "@tabler/icons-vue";
import api from '@/utils/api.js'
import { useRouter } from "vue-router";
import { clearLocalStorage } from "@/utils/helper";
import { ref } from "vue";

const router = useRouter();
const loading = ref(false);
const authUser = ref(JSON.parse(localStorage.getItem('user')));

const logout = async () => {
    loading.value = true;
    await api.post('/logout').then((res) => {
        if (res.status == 200) {
            router.push({
                name: 'login'
            })
            clearLocalStorage();
            authUser.value = null
        }
        loading.value = false;
    }).catch((error) => {
        console.error('logging logout error', error);
    })
}

const goToProfile = () => {
    router.push({
        name: 'profile'
    });
}

</script>
