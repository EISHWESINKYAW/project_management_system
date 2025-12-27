<template>
    <div :class="['flex items-center', parentClass]">
        <button :type="type" :class="['px-3 py-1 relative capitalize cursor-pointer', customClasses]">
            <div v-if="isLoading"
                class="z-10 absolute top-0 left-0 right-0 bottom-0 flex justify-center items-center rounded-md loading-wrapper">
                <SpinLoading />
            </div>
            <div :class="['button-content flex justify-center items-center gap-x-2', isLoading ? 'opacity-0' : '']">
                <component v-if="icon" :is="icon" class="text-xl" />
                {{ name }}
            </div>
        </button>
    </div>
</template>

<script setup>

const props = defineProps({
    type: {
        type: String,
        default: 'button'
    },
    name: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: ''
    },
    customClasses: {
        type: String,
        default: ''
    },
    isLoading: {
        type: Boolean,
        default: false
    },
    parentClass: {
        type: String,
        default: ''
    }
});
</script>

<style>
.loading-wrapper {
    background: var(--color-primary);
    opacity: 0.45;
}
</style>
