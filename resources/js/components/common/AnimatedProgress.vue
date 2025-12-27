<template>
    <div class="circle-progress" role="progressbar" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100"
        :style="`--value: ${animatedProgress}; --color: ${currentColor}`">
        <div class="circle-inner">
            {{ progress }}%
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue'

// Props
const props = defineProps({
    progress: {
        type: Number,
        required: true,
        default: 0,
    },
    duration: {
        type: Number,
        default: 800, // animation duration in ms
    }
})

const animatedProgress = ref(props.progress)

// Animate from current value to new value smoothly
function animateValue(from, to, duration = 800) {
    const start = performance.now()

    function frame(time) {
        const elapsed = time - start
        const ratio = Math.min(elapsed / duration, 1)
        animatedProgress.value = from + (to - from) * ratio

        if (ratio < 1) requestAnimationFrame(frame)
    }

    requestAnimationFrame(frame)
}

// Trigger animation when progress updates
watch(() => props.progress, (newVal) => {
    animateValue(animatedProgress.value, newVal, props.duration)
})

// Color based on progress %
const currentColor = computed(() => {
    if (animatedProgress.value <= 33) return '#ef4444'   // red-500
    if (animatedProgress.value <= 66) return '#facc15'   // yellow-400
    return '#22c55e'                                      // green-500
})
</script>

<style scoped>
@property --percentage {
    syntax: '<number>';
    inherits: true;
    initial-value: 0;
}

.circle-progress {
    --size: 150px;
    --stroke: -16px;
    --percentage: var(--value);
    --color: #28c76f;
    --background: #e5e7eb;

    width: var(--size);
    height: var(--size);
    border-radius: 50%;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    box-shadow: 0 4px 6px rgba(219, 139, 139, 0.9);

    transition:
        --percentage 0.6s ease,
        --color 0.4s ease;
}

.circle-progress::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background-image: conic-gradient(var(--color) calc(var(--percentage) * 1%),
            var(--background) 0);
    mask: radial-gradient(farthest-side,
            transparent calc(50% - var(--stroke)),
            black calc(50% - var(--stroke) + 1px));
    -webkit-mask: radial-gradient(farthest-side,
            transparent calc(50% - var(--stroke)),
            black calc(50% - var(--stroke) + 1px));
    transition: background-image 0.6s ease;
}

.circle-inner {
    position: relative;
    z-index: 1;
    font-size: 1rem;
    font-weight: 600;
    color: var(--color);
    animation: pulseText 2s infinite ease-in-out;
}

@keyframes pulseText {

    0%,
    100% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.05);
    }
}
</style>
