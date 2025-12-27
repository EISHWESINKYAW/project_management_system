<template>
    <div class="task-card bg-hover rounded p-4 relative">
        <div class="task-card-title mb-2">
            <div class="">
                <h2 class="h-5 hide-text rounded mb-1"></h2>
                <p class="h-4 hide-text rounded mb-1"></p>
            </div>
        </div>

        <div class="task-details mt-2">
            <div class="project-status flex justify-between items-center mb-1">
                <p class="h-4 w-[55px] hide-text rounded">Status:</p>
                <p class="h-4 w-[65px] hide-text rounded">
                </p>
            </div>
            <div class="project-status flex justify-between items-center mb-1">
                <p class="h-4 w-[90px] hide-text rounded">Created By:</p>
                <p class="h-4 w-[45px] hide-text rounded"></p>
            </div>
            <div class="project-status flex justify-between items-center py-1">
                <!-- Left: Date -->
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-[25px] h-3 hide-text rounded">Due:</span>
                    <span class="w-[125px] h-3 hide-text rounded"></span>
                </div>
                <!-- Right: Collaborator Avatars -->
                <div class="flex items-center gap-1 ml-auto">
                    <div class="w-6 h-6 rounded-full hide-text">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { checkPermissions } from '@/utils/helper.js'
import { IconEye } from '@tabler/icons-vue';
import { useRouter } from 'vue-router';

const props = defineProps({
    task: {
        type: Object,
        required: true
    }
})

const router = useRouter();

const handleTaskView = (id) => {
    router.push({
        name: 'task-detail',
        params: { id }
    })
}

const getTaskStatusColorClass = (status) => {
    switch (status) {
        case 'in_progress':
            return 'bg-warning text-on-warning';
        case 'completed':
            return 'bg-success text-on-success';
        case 'overdue':
            return 'bg-error text-on-surface';
        case 'pending':
            return 'bg-secondary text-on-surface';
        default:
            return 'bg-grey-200 text-grey-600';
    }
};
</script>
