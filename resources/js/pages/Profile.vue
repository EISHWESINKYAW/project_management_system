<template>
    <main-layout>
        <div class="container text-white">
            <div class="grid grid-cols-12 gap-4">
                <!-- profile photo and info area -->
                <div
                    class="col-span-4 relative flex flex-col justify-center bg-surface text-on-surface p-5 rounded-md box-shadow">
                    <div class="absolute top-5 right-5">
                        <SmallButton :name="''" customClasses="bg-primary text-on-primary rounded-md py-2"
                            @click="() => showModel = true" :icon="IconKeyFilled" />
                    </div>

                    <!-- change password model -->
                    <Model title="Change Password" :visible="showModel" @dismiss="closeModel" @submit="changePassword">
                        <div class="text-white rounded-md pt-4">
                            <label class="text-sm text-nowrap">Old Password</label>
                            <InputBox v-model="form.old_password" placeholder="******" type="password" />
                        </div>
                        <div class="text-white rounded-md mb-3">
                            <label class="text-sm text-nowrap">Password</label>
                            <InputBox v-model="form.password" placeholder="******" type="password" />
                        </div>
                        <div class="text-white rounded-md">
                            <label class="text-sm text-nowrap">Confirm Password</label>
                            <InputBox v-model="form.confirm_password" placeholder="******" type="password" />
                        </div>
                    </Model>

                    <div class="flex flex-col justify-center items-center gap-3">
                        <div class="flex justify-center">
                            <div
                                class="flex justify-center items-center w-[200px] h-[200px] overflow-hidden rounded-full">
                                <FileUpload :customClass="'w-full h-full rounded-full'"
                                    :modelValue="form.profile ?? '/images/user.png'" v-model="form.profile" />
                            </div>
                        </div>
                        <div class="flex flex-col justify-center items-center gap-3 mt-5">
                            <p class="text-2xl font-semibold text-center">{{ user.name }}</p>
                            <p>{{ user.email }}</p>
                            <p>{{ user.phone }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-span-8 bg-surface text-on-surface p-5 rounded-md box-shadow">
                    <table class="w-full">
                        <tbody>
                            <tr class="border-b border-gray-500">
                                <td class="py-3 ">
                                    <label for="name">Name</label>
                                </td>
                                <td class="py-3">
                                    <InputBox :type="'text'" v-model="form.name" :placeholder="'John Doe'"
                                        :readonly="isReadonly.name" @edit="isReadonly.name = false" />
                                </td>
                            </tr>

                            <tr class="border-b border-gray-500 ">
                                <td class="py-3 ">
                                    <label for="email">Email</label>
                                </td>
                                <td class="py-3">

                                    <InputBox :type="'text'" v-model="form.email" :placeholder="'example@gmail.com'"
                                        :readonly="isReadonly.email" @edit="isReadonly.email = false" />
                                </td>
                            </tr>
                            <tr class="border-b border-gray-500 ">
                                <td class="py-3 ">
                                    <label for="phone">Phone</label>
                                </td>
                                <td class="py-3">

                                    <InputBox :type="'tel'" v-model="form.phone" :placeholder="'09xxxxxxxxx'"
                                        :readonly="isReadonly.phone"
                                        @input="form.phone = form.phone.replace(/[^0-9]/g, '')"
                                        @edit="isReadonly.phone = false" />
                                </td>
                            </tr>
                            <tr class="border-b border-gray-500 ">
                                <td class="py-3 ">
                                    <label for="email">Gender</label>
                                </td>
                                <td class="py-3">

                                    <InputBox :type="'text'" v-model="form.gender" :placeholder="'Male'"
                                        :readonly="isReadonly.gender" @edit="isReadonly.gender = false" />
                                </td>
                            </tr>
                            <tr class="border-b border-gray-500 ">
                                <td class="py-3 ">
                                    <label for="email">Education</label>
                                </td>
                                <td class="py-3">

                                    <InputBox :type="'text'" v-model="form.education" :placeholder="'Under graduate'"
                                        :readonly="isReadonly.education" @edit="isReadonly.education = false" />
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 ">
                                    <label for="email">NRC NO</label>
                                </td>
                                <td class="py-3">
                                    <InputBox v-if="isReadonly.nrc_no == true" :type="'text'" v-model="form.nrc_no"
                                        :placeholder="'12/EXAMPLE(N)123456'" :readonly="true"
                                        @edit="isReadonly.nrc_no = false" />
                                    <NrcSelector v-model="form.nrc_no" v-if="isReadonly.nrc_no == false" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="w-full col-span-12 bg-surface text-on-surface p-5 rounded-md box-shadow">
                    <!-- <div class="">
                        <label for="address">Address : </label>
                    </div> -->
                    <div class="flex gap-5 py-2">
                        <h1>Address : </h1>
                        <p>{{ form.address }}</p>
                    </div>
                    <!-- <div class="py-2">
                        <InputBox v-model="form.address" :type="'text'" placeholder="No.13, SeintJ Street, ..." :rows="4"
                            :readonly="isReadonly.address" @edit="isReadonly.address = false" />
                    </div> -->
                </div>
            </div>
            <div v-if="showBtn" class="grid justify-items-end mt-3">
                <SmallButton :name="'update'" :customClasses="'bg-blue-500 rounded-md py-2'" @click="updateProfile" />
            </div>
        </div>
    </main-layout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import api from '@/utils/api.js';
import { toast } from 'vue3-toastify';
import { useUser } from '@/composables/useUser.js';
import { useRouter } from 'vue-router';
import { clearLocalStorage } from '@/utils/helper';
import { IconKeyFilled } from '@tabler/icons-vue';


const { setUser } = useUser();

const user = ref(JSON.parse(localStorage.getItem('user')));

const form = reactive({
    name: user.value.name,
    email: user.value.email,
    phone: user.value.phone,
    address: user.value.address,
    gender: user.value.gender,
    nrc_no: user.value.nrc_no,
    education: user.value.education,
    old_password: '',
    password: '',
    confirm_password: '',
    profile: user.value?.profile_url,
})
const isReadonly = reactive({
    name: true,
    email: true,
    phone: true,
    address: true,
    gender: true,
    nrc_no: true,
    education: true,
    old_password: true,
    password: true,
    confirm_password: true,
});

const showModel = ref(false);
const showBtn = ref(false);
const router = useRouter();

watch(form, (newValue) => {
    if (newValue.name !== user.value.name || newValue.profile !== user.value.profile || newValue.email !== user.value.email || newValue.phone !== user.value.phone || newValue.gender !== user.value.gender || newValue.education !== user.value.education || newValue.nrc_no !== user.value.nrc_no || (newValue.address !== user.value.address && newValue.address !== null && newValue.address !== '' && newValue.address !== undefined)) {
        showBtn.value = true;
    } else {
        showBtn.value = false;
    }
}, { deep: true });

const updateProfile = async () => {
    api.postWithFile(`/users/profile/${user.value.id}`, createPayload())
        .then((res) => {
            if (res && res.status === 200) {
                setUser(res.data);
                toast('Profile updated successfully', {
                    autoClose: 2000,
                    type: 'success'
                });
            } else {
                toast('Profile update failed', {
                    autoClose: 2000,
                    type: 'error'
                });
            }
        }).catch((err) => {
            console.log(err);
            toast('Profile update failed', {
                autoClose: 2000,
                type: 'error'
            });
        });
    form.name = user.value.name;
    form.email = user.value.email;
    form.phone = user.value.phone;
    form.gender = user.value.gender;
    form.education = user.value.education;
    form.nrc_no = user.value.nrc_no;
    form.address = user.value.address;
};

const createPayload = () => {
    const payload = {
        name: form.name,
        email: form.email,
        phone: form.phone,
        address: form.address,
        gender: form.gender,
        nrc_no: form.nrc_no,
        education: form.education,
        profile: form.profile ? form.profile[0] : null
    }
    return payload;
}

const closeModel = () => {
    showModel.value = false;
    form.password = '';
    form.confirm_password = '';
}

const changePassword = async () => {
    if (form.password !== form.confirm_password) {
        toast('Password and confirm password do not match', {
            autoClose: 2000,
            type: 'error'
        });
        showModel.value = false;
        form.password = '';
        form.confirm_password = '';
        return;
    }
    api.post(`/change-password/${user.value.id}`, { old_password: form.old_password, password: form.password, confirm_password: form.confirm_password })
        .then((res) => {
            if (res && res.status === 200) {
                toast('Password changed successfully', {
                    autoClose: 2000,
                    type: 'success'
                });
                showModel.value = false;
                form.password = '';
                form.confirm_password = '';

                setTimeout(() => {
                    clearLocalStorage();
                    router.push('/');
                }, 2000);

            }
            else {
                toast('failed', {
                    autoClose: 2000,
                    type: 'error'
                });
            }
        }).catch((err) => {
            toast(err.response.data.message, {
                autoClose: 2000,
                type: 'error'
            });
        });
}
</script>
