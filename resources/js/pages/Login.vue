<template>
    <div class="h-screen">
        <div class="relative h-full">
            <div class="relative grid grid-cols-2 h-full">
                <div class="theme-toggler absolute right-5 top-5 z-1 px-4 cursor-pointer text-on-surface hover:bg-hover w-10 h-10 rounded-full flex items-center justify-center"
                    @click="toggleDark()">
                    <div class="icon-wrapper">
                        <IconMoon v-if="isDark" class="w-7 h-7" />
                        <IconSun v-else class="w-7 h-7" />
                    </div>
                </div>
                <div class="relative h-full bg-surface text-on-surface">
                    <div class="flex justify-center items-center">
                        <img class="w-[80%]" src="/images/loginlogo.svg" alt="Logo" />
                    </div>
                </div>

                <div class=" bg-background text-on-background h-full flex justify-center items-center">
                    <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
                        <div>
                            <div class="mb-5 sm:mb-8">
                                <h1 class="mb-8 text-center font-semibold text-2xl">Task Assign Management System</h1>
                                <!-- <span class="mb-2 font-semibold text-xl">
                                    Let’s Get Things Done Together
                                </span> -->
                                <p class="text-base text-gray-400 text-center mb-5">
                                    Let’s Get Things Done Together!
                                </p>
                                <p class="text-base text-gray-400 text-center">Access your tasks, collaborate with your team, </p>
                                <p class="text-base text-gray-400 text-center">and keep projects on track.</p>
                            </div>
                            <div>
                                <form @submit.prevent="handleSubmit">
                                    <div class="space-y-5">
                                        <!-- Email -->
                                        <div class="py-2">
                                            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-400">
                                                Email<span class="text-error-500">*</span>
                                            </label>
                                            <InputBox v-model="email" type="email" id="email" name="email"
                                                placeholder="info@gmail.com" />
                                        </div>
                                        <!-- Password -->
                                        <div class="py-2">
                                            <label for="password"
                                                class="mb-1.5 block text-sm font-medium text-gray-400">
                                                Password<span class="text-error-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <InputBox v-model="password" :type="showPassword ? 'text' : 'password'"
                                                    id="password" placeholder="Enter your password" />
                                            </div>
                                        </div>

                                        <!-- Button -->
                                        <div class="py-2">
                                            <SmallButton type="submit" name="Login" :is-loading="loading"
                                                custom-classes="bg-primary text-on-primary px-3 py-2 rounded-md  w-full"
                                                :icon="IconLogin2" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <DotLoading v-if="loading" :show-dots="false" />
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useDark, useToggle } from '@vueuse/core'
import { useRouter } from 'vue-router';
import api from '@/utils/api.js';
import { IconSun, IconMoon, IconLogin2 } from '@tabler/icons-vue';
import { setLocalStorage } from '@/utils/helper';
import { toast } from 'vue3-toastify';

const isDark = useDark({
    selector: 'body',
    attribute: 'data-theme',
    valueDark: 'dark',
})
const toggleDark = useToggle(isDark)

const router = useRouter();
const email = ref('')
const password = ref('')
const showPassword = ref(false)
const loading = ref(false);

const handleSubmit = () => {
    loading.value = true;
    api.post('/login', {
        email: email.value,
        password: password.value,
    }).then((response) => {
        console.log("login success response: ", response)
        setLocalStorage(response);
        loading.value = false;
        router.push({ name: 'dashboard' });
    }).catch((error) => {
        console.log('login error here: ', error)
        loading.value = false;
        if (error.response && error.response.status === 401) {
            toast.error('Invalid credentials. Please try again.', {
                autoClose: 2000,
                type: 'error'
            });
        } else if (error.response && error.response.status === 500) {
            toast.error('Server error. Please try again later.', {
                autoClose: 2000,
                type: 'error'
            });
        } else if (error.response && error.response.status === 403) {
            toast.error(error.response.data.message, {
                autoClose: 2000,
                type: 'error'
            });
        } else {
            toast.error('An unexpected error occurred. Please try again.', {
                autoClose: 2000,
                type: 'error'
            });
        }
    });
}
</script>
