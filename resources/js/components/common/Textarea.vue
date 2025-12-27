<template>
    <div class="w-full">
        <div class="relative w-full">
            <textarea v-model="inputValue" :rows="rows" :placeholder="placeholder" :readonly="readonly" ref="EditInput"
                :disabled="disabled"
                class="bg-surface text-on-surface w-full rounded-lg border px-4 py-2.5 focus:border-primary focus:outline-none border-border disabled:opacity-60 disabled:cursor-not-allowed" />
            <div v-if="readonly" @click="EditText" class="absolute top-3 right-0 h-full w-10 cursor-pointer">
                <IconEdit />
            </div>
        </div>
    </div>
</template>

<script setup>
import { IconEdit } from '@tabler/icons-vue';
import { ref } from 'vue';

defineProps({
    placeholder: {
        type: String,
        default: "type message here"
    },
    rows: {
        type: Number,
        default: 3
    },
    readonly: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    }
})

const inputValue = defineModel();
const emits = defineEmits(['edit']);
const EditInput = ref(null);
const EditText = () => {
    emits('edit');
    EditInput.value.focus();
}
</script>
