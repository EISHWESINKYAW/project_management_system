<template>
    <div class="z-10 overflow-hidden absolute top-0 left-0 right-0 bottom-0 flex justify-center items-center"
        :class="customClass ? customClass : 'loading-wrapper'">
        <div v-if="showDots" class="dots"></div>
    </div>
</template>
<script setup>
const props = defineProps({
    showDots: {
        type: Boolean,
        default: true
    },
    customClass: {
        type: String,
        default: '',
    },
});
</script>
<style>
.dots {
    width: 56px;
    height: 26.9px;
    background: radial-gradient(circle closest-side, var(--color-primary) 90%, #0000) 0% 50%,
        radial-gradient(circle closest-side, var(--color-primary) 90%, #0000) 50% 50%,
        radial-gradient(circle closest-side, var(--color-primary) 90%, #0000) 100% 50%;
    background-size: calc(100%/3) 13.4px;
    background-repeat: no-repeat;
    animation: dots-7ar3yq 1s infinite linear;
}

.loading-wrapper {
    background: var(--color-background);
    opacity: 50%;
}

@keyframes dots-7ar3yq {
    20% {
        background-position: 0% 0%, 50% 50%, 100% 50%;
    }

    40% {
        background-position: 0% 100%, 50% 0%, 100% 50%;
    }

    60% {
        background-position: 0% 50%, 50% 100%, 100% 0%;
    }

    80% {
        background-position: 0% 50%, 50% 50%, 100% 100%;
    }
}
</style>
