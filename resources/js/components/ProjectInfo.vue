<template>
    <!-- project info section start here  -->
    <div class="project-info border border-hover hover:border-gray-300 box-shadow rounded p-4 mb-5">
        <div class="project-general-info-card">
            <h1 class="project-title text-lg font-semibold line-clamp-2"> {{ project.name }} </h1>
            <p class="project-creator text-sm text-grey-600 pb-2"> Created by: {{ project.created_by
                }} </p>
            <p class="project-description text-justify leading-7"> {{ project.description }} </p>
            <div class="project-progress pt-4">
                <h1 class="status text-sm pb-2">Project Progress</h1>
                <div class="progress-bar bg-background rounded-md relative">
                    <div :class="['progress bg-primary h-4.5', calculateProgress(project.completed_tasks, project.total_tasks) == 100 ? 'rounded-md' : 'rounded-l-md']"
                        :style="{ width: `${calculateProgress(project.completed_tasks, project.total_tasks)}%` }">
                        <span
                            class="progress-percentage text-on-primary text-sm absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2">
                            {{ calculateProgress(project.completed_tasks, project.total_tasks)
                            }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="project-status-info">
            <div class="project-status flex justify-between items-center pt-4">
                <p class="status-label">Status:</p>
                <p :class="['status-value text-sm px-1 rounded capitalize', getTaskStatusColorClass(project.status)]">
                    {{
                        project.status.replace('_', ' ') }}</p>
            </div>
            <!-- <div class="project-creator flex justify-between items-center pt-2">
                <p class="creator-label">Priority:</p>
                <p class="creator-name text-sm bg-error text-on-surface px-1 rounded">High</p>
            </div> -->
            <div class="project-collaborators flex justify-between items-center pt-2">
                <p class="collaborators-label">Created At:</p>
                <p class="collaborators-value">{{ project.created_at }}</p>
            </div>
            <div class="project-deadline flex justify-between items-center pt-2">
                <p class="deadline-label">Deadline:</p>
                <p class="deadline-value">{{ project.due_date }}</p>
            </div>
            <div class="project-deadline flex justify-between items-center pt-2">
                <p class="deadline-label">Day Left:</p>
                <p
                :class="[
                calculateDuration(project.due_date) === 'Overdue'
                    ? 'text-red-500'
                    : 'text-green-500',
                'deadline-value'
                ]"
            >
                {{ calculateDuration(project.due_date) }}
            </p>
            </div>
        </div>
    </div>
    <!-- project info section end here  -->
</template>

<script setup>
const props = defineProps({
    project: {
        type: Object,
        required: true,
    }
})

const calculateProgress = (completedTasks, totalTasks) => {
    if (totalTasks === 0) return 0;
    return Math.round((completedTasks / totalTasks) * 100);
};

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

const calculateDuration = (dueDate) => {
    const today = new Date();
    const due = new Date(dueDate);
    const diffTime = due - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays >= 0 ? `${diffDays} days left` : 'Overdue';
};
</script>
