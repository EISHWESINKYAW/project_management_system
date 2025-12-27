<template>
    <VueDatePicker v-model="dateValue" :placeholder="placeholder" :enable-time-picker="false" dark
        :allowed-dates="allowedDates" auto-apply :disabled="disabled" />
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    enableTimePicker: {
        type: Boolean,
        default: false
    },
    placeholder: {
        type: String,
        default: 'Pick a date..'
    },
    disabled: {
        type: Boolean,
        default: false
    }

})
const dateValue = defineModel()

const allowedDates = computed(() => {
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Normalize to midnight for accurate comparison

    const daysAfterToday = 365;
    const result = [];

    for (let i = 0; i <= daysAfterToday; i++) {
        const date = new Date();
        date.setDate(today.getDate() + i);
        result.push(new Date(date));
    }

    return result;
});
</script>

<style>
/* .dp__input {
    height: 44px !important;
    background: var(--color-surface) !important;
    border-color: var(--color-border) !important;
    border-radius: 0.5rem !important;
}

.dp__input:hover {
    border-color: var(--color-border) !important;
} */

.dp__theme_dark {
    --dp-background-color: var(--color-surface);
    --dp-text-color: var(--color-on-surface);
    --dp-hover-color: var(--color-hover);
    --dp-hover-text-color: var(--color-on-surface);
    --dp-hover-icon-color: #959595;
    --dp-primary-color: var(--color-primary);
    --dp-primary-disabled-color: #61a8ea;
    --dp-primary-text-color: var(--color-on-primary);
    --dp-secondary-color: var(--color-secondary);
    --dp-border-color: var(--color-border);
    --dp-menu-border-color: var(--color-border);
    --dp-border-color-hover: var(--color-border);
    --dp-border-color-focus: var(--color-border);
    --dp-disabled-color: transparent;
    --dp-disabled-color-text: #d0d0d0;
    --dp-scroll-bar-background: #212121;
    --dp-scroll-bar-color: #484848;
    --dp-success-color: #00701a;
    --dp-success-color-disabled: #428f59;
    --dp-icon-color: #959595;
    --dp-danger-color: #e53935;
    --dp-marker-color: #e53935;
    --dp-tooltip-color: #3e3e3e;
    --dp-highlight-color: rgb(0 92 178 / 20%);
    --dp-range-between-dates-background-color: var(--dp-hover-color, #484848);
    --dp-range-between-dates-text-color: var(--dp-hover-text-color, #fff);
    --dp-range-between-border-color: var(--dp-hover-color, #fff);
    --dp-loader: 5px solid var(--color-primary);
}
</style>
