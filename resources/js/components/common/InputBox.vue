<template>
    <div class="w-full">
        <div class="relative w-full">
            <input ref="EditInput" v-model="inputValue" :type="inputType" :placeholder="placeholder"
                :readonly="readonly" :disabled="disabled"
                class="w-full border ps-3 rounded-md bg-surface h-[38px] border-border focus:border-primary focus:outline-none disabled:opacity-60 disabled:cursor-not-allowed" />
            <div class="show-password absolute right-4 top-1/2 -translate-y-1/2" v-if="type == 'password'"
                @click="toggleShowPassword">

                <IconEye v-if="inputType == 'text'" stroke={2} class="text-white cursor-pointer" />
                <IconEyeOff v-else stroke={2} class="text-white cursor-pointer" />

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { IconEye, IconEyeOff } from '@tabler/icons-vue';

const props = defineProps({
    placeholder: {
        type: String,
        default: "Type something here"
    },
    type: {
        type: String,
        default: 'text'
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

const EditInput = ref(null);

const inputValue = defineModel();
const inputType = ref(props.type);
const emits = defineEmits(['edit']);

const toggleShowPassword = () => {
    inputType.value = inputType.value == "text" ? "password" : "text";
};
</script>
