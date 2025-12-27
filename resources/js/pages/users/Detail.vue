<template>
    <div>
        <PageHeader :title="userDetail?.name || 'User Detail'" :breadcrumbs="breadcrumbs" :show-action-btn="false" />
        <div class="flex grid-cols-2 gap-2 mt-3">
            <!-- User Info Card -->
            <div class="relative bg-surface text-on-surface rounded shadow w-[50%] mt-[80px]">
                <div class="flex flex-col items-center justify-center px-8 mt-3">
                    <div class=" absolute z-10 -top-[80px] w-[140px] h-[140px] overflow-hidden rounded-full">
                        <FileUpload
                            :customClass="'w-full h-full rounded-full object-cover'"
                            v-model="userForm.profile"
                            :imgStyle="{ width: '140px', height: '140px', objectFit: 'cover', borderRadius: '50%' }"
                        />
                    </div>
                    <div class="w-full">
                        <p class="text-center text-xl font-bold mt-16 mb-3">{{ userForm.name }}</p>

                        <table class="w-full px-4">
                            <tbody>
                                <!-- <tr class="border-b border-gray-700">
                                        <td class="py-3 ">
                                            <label for="name">Name</label>
                                        </td>
                                        <td class="py-3">
                                            <InputBox :type="'text'" v-model="userForm.name" :placeholder="'John Doe'"
                                                :readonly="isReadonly.name" @edit="isReadonly.name = false" />
                                        </td>
                                    </tr> -->

                                <tr class="border-b border-gray-700 ">
                                    <td class="py-3 px-2">
                                        <label for="email">Email</label>
                                    </td>
                                    <td class="py-3 px-2">

                                        <InputBox :type="'text'" v-model="userForm.email"
                                            :placeholder="'example@gmail.com'" :readonly="isReadonly.email"
                                            @edit="isReadonly.email = false" />
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-700 ">
                                    <td class="py-3 px-2">
                                        <label for="phone">Phone</label>
                                    </td>
                                    <td class="py-3 px-2">
                                        <InputBox :type="'tel'" v-model="userForm.phone" :placeholder="'09xxxxxxxxx'"
                                            :readonly="isReadonly.phone"
                                            @input="userForm.phone = userForm.phone.replace(/[^0-9]/g, '')"
                                            @edit="isReadonly.phone = false" />
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-700 ">
                                    <td class="py-3 px-2">
                                        <label for="gender">Gender</label>
                                    </td>
                                    <td class="py-3 px-2">
                                        <InputBox :type="'text'" v-model="userForm.gender" :placeholder="'Male'"
                                            :readonly="isReadonly.gender" @edit="isReadonly.gender = false" />
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-700 ">
                                    <td class="py-3 px-2">
                                        <label for="education">Education</label>
                                    </td>
                                    <td class="py-3 px-2">
                                        <InputBox :type="'text'" v-model="userForm.education"
                                            :placeholder="'Under graduate'" :readonly="isReadonly.education"
                                            @edit="isReadonly.education = false" />
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-700 ">
                                    <td class="py-3 px-2">
                                        <label for="nrc">NRC NO</label>
                                    </td>
                                    <td class="py-3 px-2">
                                        <InputBox v-if="isReadonly.nrc_no == true" :type="'text'"
                                            v-model="userForm.nrc_no" :placeholder="'12/EXAMPLE(N)123456'"
                                            :readonly="true" @edit="isReadonly.nrc_no = false" />
                                        <NrcSelector v-model="userForm.nrc_no" v-if="isReadonly.nrc_no == false" />
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-700 ">
                                    <td class="py-3 px-2">
                                        <label for="nrc">Address</label>
                                    </td>
                                    <td class="py-3 px-2">
                                        <InputBox v-if="isReadonly.address == true" :type="'text'"
                                            v-model="userForm.address" :placeholder="'address'" :readonly="true"
                                            @edit="isReadonly.address = false" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-2">
                                        <label for="createdAt">Created At</label>
                                    </td>
                                    <td class="py-3 px-2">
                                        <InputBox v-if="isReadonly.nrc_no == true" :type="'text'"
                                            v-model="userForm.created_at" :placeholder="'created date'" :readonly="true"
                                            @edit="isReadonly.nrc_no = false" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- <div class="w-full flex justify-between mt-4 gap-2">
                <button class="btn  bg-[#7367f0] rounded-lg py-1 px-9 text-white cursor-pointer" @click="goBack">Back</button>
                <button class="btn  bg-[#7367f0] rounded-lg py-1 px-9 text-white cursor-pointer" @click="editUser">Edit</button>
            </div> -->
                <!-- Activity/Tasks Section -->
                <!-- <div class="">
            <h3 class="text-lg font-semibold mb-2">Recent Tasks</h3>
            <TaskList :userId="user.id" />
        </div> -->
            </div>
            <div class=" bg-surface text-on-surface rounded shadow p-6 w-[50%] h-auto">
                <h2 class="text-2xl font-semibold mb-4">Assigned Tasks</h2>
                <div v-if="loading" class="flex justify-center items-center h-32">
                    <LoadingSpinner />
                </div>
                <div v-else>
                    <div v-if="userDetail?.tasks.length">
                        <div v-for="task in userDetail.tasks" :key="task.id" class="mb-6 p-4 rounded-lg shadow border border-gray-500">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="text-md font-[600]">{{ task.name }}</h3>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                      :class="{
                                          'bg-green-100 text-green-700': task.status === 'completed',
                                          'bg-yellow-100 text-yellow-700': task.status === 'in_progress',
                                          'bg-red-100 text-red-700': task.status === 'overdue',
                                          'bg-gray-100 text-yellow-700': task.status === 'pending'
                                      }">
                                    {{ task.status.replace('_', ' ') }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-400 mb-1">
                                <span class="font-semibold">Project:</span> {{ task.project.name }}
                            </div>
                            <div class="text-sm text-gray-400 mb-1">
                                <span class="font-semibold">Due Date:</span> {{ task.due_date }}
                            </div>
                            <!-- <div class="text-sm text-gray-600">
                                <span class="font-semibold">Description:</span> {{ task.description || 'No description provided.' }}
                            </div> -->
                        </div>
                    </div>
                    <p v-else class="text-center text-gray-500">No tasks assigned.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import PageHeader from '@/components/common/PageHeader.vue';
import api from '@/utils/api';

const route = useRoute();
const router = useRouter();
const user = ref({});
const loading = ref(false);
const userDetail = ref(null);
const userId = ref(route.params.id);


const breadcrumbs = [
    {
        name: "Home",
        route: "/",
    },
    {
        name: "UserList",
        route: "/user/list",
    },
    {
        name: "User Detail",
        route: "",
    },
];

const userForm = reactive({
    name: '',
    email: '',
    role: '',
    profile: '',
    gender: '',
    education: '',
    address: '',
    password: '',
    created_at: '',
});

const isReadonly = reactive({
    name: true,
    email: true,
    phone: true,
    gender: true,
    education: true,
    nrc_no: true,
    address: true,
    created_at: true
});


// const fetchUserDetailll = async (id) => {
//     try {
//         loading.value = true;
//         const response = await api.get(`api/users/detail/${id}`);
//         const user = response.data.data;
//         userForm.name = user.name || '';
//         userForm.email = user.email || '';
//         userForm.profile = user.profile || '';
//         userForm.password = '';
//         openProjectAddModel.value = true;
//     } catch (error) {
//         console.error('Failed to fetch user detail:', error);
//     } finally {
//         loading.value = false;
//     }
// };

const fetchUserDetail = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/users/detail/${userId.value}`);
        userDetail.value = response.data.data;
        console.log('User Detail:', response.data.data);
        userForm.name = userDetail.value.name;
        userForm.profile = userDetail.value.profile_url
        userForm.email = userDetail.value.email;
        userForm.phone = userDetail.value.phone;
        userForm.gender = userDetail.value.gender;
        userForm.education = userDetail.value.education;
        userForm.profile = userDetail.value.profile_url;
        userForm.nrc_no = userDetail.value.nrc_no;
        userForm.address = userDetail.value.address;
        userForm.created_at = userDetail.value.created_at;
    } catch (error) {
        console.error('Error fetching user detail:', error);
    } finally {
        loading.value = false;
    }
};
const editUser = () => {
    router.push({ name: 'UserEdit', params: { id: user.value.id } });
};

const goBack = () => {
    router.back();
};

onMounted(() => {
    fetchUserDetail();
});

</script>
