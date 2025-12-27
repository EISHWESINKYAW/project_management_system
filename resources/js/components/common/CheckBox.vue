<template>
    <label :for="id" :class="['flex items-center w-fit text-sm font-medium select-none text-on-surface',
        disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer '
    ]">
        <div class="relative">
            <input type="checkbox" :id="id" v-model="checkbox" class="sr-only" :disabled="disabled"
                @change="handleChange" />
            <div :class="checkbox
                ? 'border-primary bg-primary'
                : 'bg-transparent border-pimary'
                " class="flex h-5 w-5 items-center justify-center rounded-md border-[1.25px] hover:border-primary">
                <span :class="checkbox ? '' : 'opacity-0'">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </div>
        </div>
    </label>
</template>

<script setup>

defineProps({
    id: {
        type: String,
        required: true
    },
    disabled: {
        type: Boolean,
        default: false
    },
})
const emit = defineEmits(['update']);

const checkbox = defineModel(false);

const handleChange = () => {
    emit('update');
}
</script>
