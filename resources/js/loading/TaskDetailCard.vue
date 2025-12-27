<template>
    <div class="project-info bg-surface text-on-surface box-shadow rounded p-4">
        <div class="project-general-info-card">
            <h1 class="h-5 my-1 w-[250px] hide-text rounded"></h1>
            <p class="h-4 my-1 w-[180px] hide-text rounded"> </p>
            <p class="h-5 my-1 w-[320px] hide-text rounded"></p>
        </div>
        <div class="project-status-info">
            <div class="project-creator flex justify-between items-center pt-2">
                <p class="h-5 my-1 w-[80px] hide-text rounded"></p>
                <p class="h-4 my-1 w-[150px] hide-text rounded"></p>
            </div>
            <div class="project-status flex justify-between items-center pt-4">
                <p class="h-5 my-1 w-[60px] hide-text rounded"></p>
                <p class="h-5 my-1 w-[90px] hide-text rounded"></p>
            </div>
            <div class="project-creator flex justify-between items-center pt-2">
                <p class="h-5 my-1 w-[70px] hide-text rounded"></p>
                <p class="h-5 my-1 w-[90px] hide-text rounded"></p>
            </div>
            <div class="project-collaborators flex justify-between items-center pt-2">
                <p class="h-5 my-1 w-[100px] hide-text rounded"></p>
                <p class="h-5 my-1 w-[120px] hide-text rounded"></p>
            </div>
            <div class="project-deadline flex justify-between items-center pt-2">
                <p class="h-5 my-1 w-[80px] hide-text rounded"></p>
                <p class="h-5 my-1 w-[90px] hide-text rounded"></p>
            </div>
            <div class="project-deadline flex justify-between items-center pt-2">
                <p class="h-5 my-1 w-[60px] hide-text rounded"></p>
                <p class="h-5 my-1 w-[80px] hide-text rounded"></p>
            </div>
            <div class="separator border-b border-hover py-2"></div>
            <div class="task-collaborator py-2">
                <label class="h-4 my-1 w-[260px] hide-text rounded">
                </label>
                <div class="grid grid-cols-4 gap-1 ml-auto">
                    <div v-for="collaborator in 4" :key="collaborator"
                        class="flex flex-col w-full items-center justify-center bg-hover rounded p-3">
                        <div class="w-12 h-12 rounded-full hide-text"></div>
                        <div class="flex flex-col w-full items-center justify-center">
                            <div class="flex flex-col items-center">
                                <div class="h-5 my-1 w-[80px] hide-text rounded"></div>
                                <div class="h-4 my-1 w-[100px] hide-text rounded"></div>
                            </div>
                            <div class="h-5 my-1 w-[110px] hide-text rounded">

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
