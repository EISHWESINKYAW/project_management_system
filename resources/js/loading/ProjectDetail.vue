<script setup>
import { IconLibraryPlusFilled } from '@tabler/icons-vue';
import ProjectCard from './ProjectCard.vue';
import TaskCard from './TaskCard.vue';
import CommentCard from './CommentCard.vue';

</script>

<template>
    <div class="project-detail-wrapper grid grid-cols-5 gap-4">
        <div class="project-info-loading col-span-2 bg-surface">
            <ProjectCard />
        </div>

        <!-- collaborator list section start here -->
        <div class="project-colaborator col-span-3 bg-surface text-on-surface box-shadow rounded p-4">
            <h1 class="project-title text-lg font-semibold pb-2"> Collaborator List </h1>
            <div class="collaborator-list flex flex-col gap-2 max-h-[360px] overflow-auto custom-scrollbar">
                <div v-for="collaborator in 4" :key="collaborator"
                    class="flex items-center bg-hover rounded p-3 gap-x-2">
                    <div class="w-12 h-12 rounded-full hide-text  border-2 border-primary"></div>
                    <div class="flex-1 flex flex-row items-center justify-between">
                        <div>
                            <div class="h-5 w-[130px] hide-text rounded my-1"></div>
                            <div class="h-4 w-[115px] hide-text rounded my-1"></div>
                        </div>
                        <div class="flex flex-col gap-y-2 text-right">
                            <span class="h-3 w-[130px] hide-text rounded my-1">

                            </span>
                            <span class="h-3 w-[125px] hide-text rounded my-1">
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- collaborator list section end here -->

        <!-- project task section start here -->
        <div class="project-task-container col-span-3 bg-surface text-on-surface box-shadow rounded p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-lg font-semibold">Tasks</h1>
                <SmallButton name="New Task" customClasses="bg-primary text-on-primary rounded py-1.5"
                    :icon="IconLibraryPlusFilled" />
            </div>
            <div class="task-list grid grid-cols-2 gap-4 max-h-[400px] overflow-auto custom-scrollbar">
                <TaskCard v-for="data in 2" :key="data" />
            </div>
        </div>
        <!-- project task section end here -->

        <!-- project comment section here  -->
        <CommentCard />
    </div>
</template>
