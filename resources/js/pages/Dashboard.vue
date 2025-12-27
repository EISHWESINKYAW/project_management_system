<template>
    <div class="dashboard content-wrapper">
        <div class="recent-projects-section bg-surface text-on-surface p-4">
            <div class="header flex justify-between items-start pb-3">
                <h1 class="text-xl font-semibold text-grey-700">Recent Projects</h1>
                <SmallButton v-if="hasAnyPermission('project')" name="See More" customClasses="bg-primary text-on-primary rounded py-1"
                    @click="seeMoreProjects()" :icon="IconLiveView" />
            </div>
            <div v-if="recentProjectLoading" class="project-loading grid grid-cols-3 gap-x-3">
                <ProjectCard v-for="data in 3" :key="data" />
            </div>
            <div v-else class="content-section">
                <div v-if="recentProjects && recentProjects.length > 0"
                    class="recent-project-list grid grid-cols-3 gap-x-3">
                    <ProjectInfo v-for="project in recentProjects" :key="project.id" :project="project" />
                </div>
                <div v-else class="empty-recent-projects text-center py-[90px]">
                    <!-- <EmptyProject /> -->
                    <!-- componentကဟာခေါ်တာအဆင်မပြေသေးလို့လောလောဆယ်ဒီလိုပဲလုပ်ထားတယ် -->
                    <img src="/images/empty-project.svg" alt="empty project" class="w-[345px] mx-auto mb-1">
                    <p class="text-md text-grey-600">You have no recent projects apparently!</p>
                </div>

            </div>
        </div>

        <!-- <div class="recent-tasks-section bg-surface text-on-surface p-4 mt-6">
            <div class="header flex justify-between items-start pb-3">
                <h1 class="text-xl font-semibold text-grey-700">Recent Tasks</h1>
                <SmallButton name="All Tasks" customClasses="bg-primary text-on-primary rounded py-1"
                    @click="seeMoreTasks()" :icon="IconLink" />
            </div>
            <div v-if="recentTaskLoading" class="task-loading grid grid-cols-3 gap-x-3">
                <TaskCard v-for="data in 3" :key="data" />
            </div>
            <div v-else class="content-section">
                <div v-if="recentTasks && recentTasks.length > 0" class="recent-tasks-list  grid grid-cols-3 gap-x-3">
                    <TaskInfo v-for="task in recentTasks" :key="task.id" :task="task" />
                </div>
                <div v-else class="empty-recent-tasks text-center">
                    <img src="/images/empty-task.svg" alt="empty project" class="w-[345px] mx-auto mb-1">

                    <p class="text-md text-grey-600">You have no recent tasks apparently!</p>
                </div>
            </div>
        </div> -->
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import api from '@/utils/api';
import ProjectInfo from '../components/ProjectInfo.vue';
import { IconLiveView, IconLink } from '@tabler/icons-vue';
import { useRouter } from 'vue-router';
import TaskInfo from '../components/TaskInfo.vue';
import ProjectCard from '../loading/ProjectCard.vue';
import TaskCard from '../loading/TaskCard.vue';
import { hasAnyPermission } from '@/utils/helper';

const recentProjectLoading = ref(true);
const recentTaskLoading = ref(true);
const recentProjects = ref(null);
const recentTasks = ref(null);
const router = useRouter();

onMounted(() => {
    fetchRecentProjects();
    fetchRecentTasks();
})

const fetchRecentProjects = async (page = 1) => {
    recentProjectLoading.value = true;
    api.get('/dashboard/recent-projects', {
        params: {
            page: page,
            per_page: 5,
        }
    }).then((res) => {
        recentProjects.value = res?.data?.data;
        recentProjectLoading.value = false
    }).catch((error) => {
        console.error(error)
    })
}

const fetchRecentTasks = async (page = 1) => {
    recentTaskLoading.value = true;
    api.get('/dashboard/recent-tasks', {
        params: {
            page: page,
            per_page: 5,
        }
    }).then((res) => {
        recentTasks.value = res?.data?.data;
        recentTaskLoading.value = false
    }).catch((error) => {
        console.error(error)
    })
}

const seeMoreProjects = () => {
    router.push({
        name: 'project-list'
    });
}

const seeMoreTasks = () => {
    router.push({
        name: 'task-list'
    })
}

</script>
