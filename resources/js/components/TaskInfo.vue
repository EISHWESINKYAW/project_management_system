<template>
    <div class="task-card bg-hover rounded p-4 relative">
        <div class="task-card-title mb-2">
            <div>
                <h2 class="task-title text-base font-semibold line-clamp-1">{{ task.name }}</h2>
                <p class="task-description text-sm text-grey-600">{{ task.description }}</p>
            </div>
            <div class="header-action flex absolute top-2 right-2">
                <IconEye v-if="checkPermissions('task.detail')" @click="handleTaskView(task.id)"
                    class="w-6 h-6 cursor-pointer text-grey-600" />
            </div>
        </div>

        <div class="task-details mt-2">
            <div class="project-status flex justify-between items-center py-1">
                <p class="status-label">Status:</p>
                <p :class="`status-value text-sm ${getTaskStatusColorClass(task.status)} px-1 rounded capitalize`">
                    {{ task.status.replace('_', ' ') }}
                </p>
            </div>
            <div class="project-status flex justify-between items-center py-1">
                <p class="status-label">Created By:</p>
                <p class="status-value text-sm">{{ task.created_by }}</p>
            </div>
            <div class="project-status flex justify-between items-center py-1">
                <!-- Left: Date -->
                <div class="flex items-center gap-2">
                    <span class="text-xs text-grey-500">Due:</span>
                    <span class="text-xs font-medium">{{ task.due_date }}</span>
                </div>
                <!-- Right: Collaborator Avatars -->
                <div class="flex items-center gap-1 ml-auto">
                    <!-- <div v-for="collaborator in task.collaborators.slice(0, 3)" :key="collaborator.id + '-avatar'"
                        class="w-6 h-6 rounded-full border-2 border-white -ml-2 first:ml-0 overflow-hidden">
                        <img :src="collaborator.profile" :alt="collaborator.name" class="w-full h-full object-cover" />
                    </div> -->
                    <span v-if="task.collaborators.length > 3"
                        class="w-6 h-6 rounded-full bg-grey-200 text-xs flex items-center justify-center -ml-2 border-2 border-white">
                        +{{ task.collaborators.length - 3 }}
                    </span>
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
