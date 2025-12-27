<template>
    <div class="w-full max-w-sm px-4">
        <Popover v-slot="{ open }" class="relative">
            <PopoverButton
                class="notifications focus:outline-none cursor-pointer hover:bg-hover w-10 h-10 rounded-full flex items-center justify-center relative">
                <span v-if="unreadCount > 0"
                    class="absolute top-0 right-0 bg-red-500 text-white text-xs font-semibold rounded-full w-4 h-4 flex items-center justify-center">
                    {{ unreadCount }}
                </span>
                <IconBellFilled class="w-6 h-6 cursor-pointer text-grey-600" />
            </PopoverButton>

            <transition enter-active-class="transition duration-200 ease-out" enter-from-class="translate-y-1 opacity-0"
                enter-to-class="translate-y-0 opacity-100" leave-active-class="transition duration-150 ease-in"
                leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-1 opacity-0">
                <PopoverPanel v-slot="{ close }"
                    class="absolute max-h-[420px] z-0 overflow-auto custom-scrollbar right-0 mt-6 w-100 origin-top-right rounded-md bg-surface text-on-surface shadow-lg ring-1 ring-gray-200/5 focus:outline-none">
                    <div class="flex flex-col">
                        <MenuItem>
                        <div class="flex items-center justify-between px-4 py-2">
                            <h1 class="text-lg font-semibold">Notifications</h1>
                            <span class="text-sm text-gray-500">{{ unreadCount }} New</span>
                        </div>
                        </MenuItem>
                        <div class="divider border-b border-border"></div>

                        <div v-if="notifications?.length > 0" class="notifications-list">
                            <div v-for="notification in notifications" :key="notification.id">
                                <div @click="notificationDetail(notification, close)"
                                    class="profile-section group flex gap-x-2 items-start rounded-md px-2 py-4 text-sm hover:bg-hover cursor-pointer">
                                    <div class="user-avatar w-1/5 ">
                                        <img :src="notification.profile ? notification.profile : 'https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/avatars/1.png'"
                                            alt="" class="w-14 h-14 rounded-full overflow-hidden" />
                                    </div>
                                    <div class="info w-full">
                                        <div class="title-box flex items-center justify-between">
                                            <h1 class="text-base font-medium">{{ notification.title }}</h1>
                                            <span v-if="!notification.read_at && !notification.read"
                                                class="inline-block w-2 h-2 rounded-full bg-primary ml-2 align-middle"></span>
                                            <div v-else class="trash-icon absolute right-5"
                                                @click="handleDeleteNoti(notification, close)">
                                                <IconTrashX class="w-5 h-5 cursor-pointer text-error " />
                                            </div>

                                        </div>
                                        <p class="text-sm line-clamp-1 font-semibold">{{ notification.description }}
                                        </p>
                                        <p><span class="text-gray-500">{{ timeAgo(notification.created_at) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="divider border-b border-border"></div>
                        </div>

                        <div v-else class="empty-notification text-center py-3">
                            <p class="text-sm text-gray-500">No notification to show for now!</p>
                        </div>

                        <div class="px-2 py-3 flex justify-center w-full">
                            <SmallButton name="View all notifications" parentClass="w-full"
                                customClasses="bg-primary text-on-primary text-sm w-full rounded py-1" />
                        </div>
                    </div>
                </PopoverPanel>
            </transition>
        </Popover>
    </div>
</template>

<script setup>
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { IconBellFilled, IconTrashX } from "@tabler/icons-vue";
import api from '@/utils/api.js'
import { useRouter } from "vue-router";
import { onMounted, ref } from "vue";
import websocket from "@/utils/websocket";
import { toast } from 'vue3-toastify';

const unreadCount = ref(0);
const notifications = ref([]);
const router = useRouter();

const authUser = JSON.parse(localStorage.getItem('user'));
const userId = authUser ? authUser.id : null;

onMounted(() => {
    socketListen();
    fetchNotifications();
});

const socketListen = () => {
    let channelInfo = {
        channel_name: `notification.${userId}`,
        event_name: '.NewNoti',
    }

    websocket.privateListen(channelInfo, (data) => {
        toast(data?.data?.description, {
            autoClose: 3000,
            type: 'success'
        })
        notifications.value.unshift(data.data);
        unreadCount.value++;
    });
}

const fetchNotifications = async () => {
    try {
        const response = await api.get('notifications/list');
        notifications.value = response.data.data;
        unreadCount.value = response.data.unread_count;
    } catch (error) {
        console.error('Error fetching notifications:', error);
    }
};

const notificationDetail = async (notification, close) => {
    console.log('detail clicked')
    if (notification.read && notification.read_at) {
        return
    }

    try {
        const response = await api.post(`notifications/mark-as-read/${notification.id}`);
        if (response.status === 200) {
            close()
            unreadCount.value -= 1;
            notification.read = true;
            notification.read_at = new Date().toISOString(); // Update read_at timestamp
            router.push({
                path: `/${notification.redirect_url}/${notification.notifiable_id}`,
            })
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
};

const handleDeleteNoti = async (noti, close) => {
    try {
        const response = await api.delete(`notifications/delete/${noti.id}`);
        if (response.status === 200) {
            close()
            toast('Notification removed successfully', {
                autoClose: 3000,
                type: 'success'
            })
        }
        notifications.value = notifications.value.filter(notification => notification.id != noti.id)
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

function timeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return `${seconds} second${seconds !== 1 ? 's' : ''} ago`;
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
    const days = Math.floor(hours / 24);
    if (days < 30) return `${days} day${days !== 1 ? 's' : ''} ago`;
    const months = Math.floor(days / 30);
    if (months < 12) return `${months} month${months !== 1 ? 's' : ''} ago`;
    const years = Math.floor(months / 12);
    return `${years} year${years !== 1 ? 's' : ''} ago`;
}

</script>

<style>
.trash-icon {
    display: none;
}

.profile-section:hover .trash-icon {
    display: inline-block;
}
</style>
