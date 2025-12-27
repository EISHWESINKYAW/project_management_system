<template>
    <div v-if="task" class="project-info bg-surface text-on-surface box-shadow rounded p-4">
        <div class="project-general-info-card">
            <h1 class="project-title text-lg font-semibold line-clamp-2"> {{ task.name }} </h1>
            <p class="project-creator text-sm text-grey-600 pb-2"> Created by: {{ task.created_by
            }} </p>
            <p class="project-description text-justify leading-7"> {{ task.description }} </p>
        </div>
        <div class="project-status-info">
            <div class="project-creator flex justify-between items-center pt-2">
                <p class="creator-label">Project:</p>
                <p class="creator-name text-sm text-on-surface px-1 rounded">{{ task.project.name }}</p>
            </div>
            <div class="project-status flex justify-between items-center pt-4">
                <p class="status-label">Status:</p>
                <p :class="['status-value text-sm px-1 rounded capitalize', getTaskStatusColorClass(task.status)]">
                    {{
                        task.status.replace('_', ' ') }}</p>
            </div>
            <!-- <div class="project-creator flex justify-between items-center pt-2">
                <p class="creator-label">Priority:</p>
                <p class="creator-name text-sm bg-error text-on-surface px-1 rounded">High</p>
            </div> -->
            <div class="project-collaborators flex justify-between items-center pt-2">
                <p class="collaborators-label">Created At:</p>
                <p class="collaborators-value">{{ task.created_at }}</p>
            </div>
            <div class="project-deadline flex justify-between items-center pt-2">
                <p class="deadline-label">Deadline:</p>
                <p class="deadline-value">{{ task.due_date }}</p>
            </div>
            <div class="project-deadline flex justify-between items-center pt-2">
                <p class="deadline-label">Day Left:</p>
                <p class="deadline-value">{{ calculateDuration(task.due_date) }}</p>
            </div>
            <div class="separator border-b border-hover py-2"></div>
            <div class="task-collaborator py-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-400 text-nowrap">
                    Collaborator
                </label>
                <div class="grid grid-cols-4 gap-1 ml-auto">
                    <div v-for="collaborator in task.collaborators" :key="collaborator.id"
                        class="flex flex-col w-full items-center justify-center bg-hover rounded p-3">
                        <img :src="collaborator.profile_url" alt="Profile"
                            class="w-12 h-12 rounded-full object-cover border-2 border-primary" />
                        <div class="flex flex-col w-full items-center justify-between">
                            <div>
                                <div class="font-semibold text-base py-1 text-nowrap">{{ collaborator.name }}</div>
                                <div class="text-sm text-grey-600 capitalize">{{ collaborator.role }}</div>
                            </div>
                            <div class="flex justify-between w-full text-right">
                                <div class=" text-grey-500 flex gap-x-1 items-center">
                                    <IconTargetArrow class="w-5" />
                                    <span v-if="collaborator.total_projects > 0"
                                        class="text-white text-xs font-semibold">
                                        <span class="pe-1">:</span>
                                        {{ collaborator.total_projects }}
                                    </span>
                                </div>
                                <div class=" text-success flex items-center gap-x-1">
                                    <IconRosetteDiscountCheck class="w-5" />
                                    <span v-if="collaborator.total_tasks_complete > 0"
                                        class="text-white text-xs font-semibold">
                                        <span class="pe-1">:</span>
                                        {{ collaborator.total_tasks_complete }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- project info section end here  -->
</template>

<script setup>
import { IconRosetteDiscountCheck, IconTargetArrow } from '@tabler/icons-vue';

const props = defineProps({
    task: {
        type: Object,
        required: true
    }
})

const getTaskStatusColorClass = (status) => {
    switch (status) {
        case 'in_progress':
            return 'bg-warning text-on-warning';
        case 'completed':
            return 'bg-success text-on-success';
        case 'overdue':
            return 'bg-error text-on-surface';
        case 'pending':
            return 'bg-secondary text-on-secondary';
        default:
            return 'bg-grey-200 text-grey-600';
    }
};

const calculateDuration = (dueDate) => {
    const today = new Date();
    const due = new Date(dueDate);
    const diffTime = due - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays >= 0 ? `${diffDays} days left` : 'Overdue';
};
</script>
