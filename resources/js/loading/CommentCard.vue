<template>
    <div class="task-container col-span-2 bg-surface text-on-surface box-shadow rounded p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-lg font-semibold">Comments</h1>
            <SmallButton customClasses="bg-primary text-on-primary rounded py-1.5" :icon="IconMessagePlus" />
        </div>
        <div class="task-list grid grid-cols-1 gap-4 max-h-[400px] overflow-auto custom-scrollbar">

            <div class="comment-cards">
                <!-- Task Card Example -->
                <div v-for="comment in 1" class="comment-section">
                    <div
                        :class="['max-w-xl mx-auto mt-2 font-sans mb-4', comment == 1 - 1 ? '' : 'border-b border-hover']">
                        <div class="flex items-start mb-2">
                            <div class="w-12 h-12 rounded-full mr-4 hide-text"></div>
                            <div class="flex-1 rounded relative">
                                <div class="h-5 w-[100px] hide-text rounded my-1">

                                </div>
                                <div class="mb-2 h-4 w-[130px] hide-text rounded"></div>
                                <div class="mb-1 flex justify-start gap-x-6">
                                    <span class="w-[60px] h-4 my-1 rounded hide-text">

                                    </span>

                                    <button
                                        class=" text-primary hover:underline hide-text text-sm font-medium cursor-pointer">Reply</button>
                                </div>
                            </div>
                        </div>
                        <div class="ml-12">
                            <div v-for="reply in 1" :key="reply" class="flex items-start mt-2 mb-5">
                                <div alt="avatar" class="w-10 h-10 rounded-full mr-3 hide-text"></div>
                                <div class="flex-1 ">
                                    <div class="h-5 w-[130px] hide-text rounded mb-1">
                                    </div>
                                    <div class="mb-2 h-4 w-[100px] hide-text rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { IconMessagePlus } from '@tabler/icons-vue';


</script>
