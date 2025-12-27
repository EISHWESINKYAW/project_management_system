<template>
    <TransitionRoot as="template" :show="openModel">
        <Dialog class="relative z-40" @close="handleClose">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-grey-500/10 backdrop-blur-[5px] transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto ">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel
                            class="relative transform bg-surface text-on-surface border border-border  rounded-lg text-left shadow-xl transition-all sm:my-8 min-w-[500px]">
                            <div class="model-body px-3 py-2">
                                <div :class="modelBodyClass">
                                    <div class="mt-3 text-left">
                                        <DialogTitle as="h3" class="text-base font-semibold">{{ title }}
                                        </DialogTitle>
                                        <slot />
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-x-4 px-3 pb-4">
                                <SmallButton name="Cancel" customClasses="bg-warning text-on-warning rounded py-1.5"
                                    @click="handleClose" />
                                <SmallButton name="Submit" customClasses="bg-primary text-on-primary rounded py-1.5"
                                    @click="handleSubmit" :is-loading="submitLoading" />
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'

const props = defineProps({
    visible: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: "Add New"
    },
    modelBodyClass: {
        type: String,
        required: false,
    },
    submitLoading: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['dismiss', 'submit'])

const openModel = ref(false)

watch(() => props.visible, (newVal) => {
    openModel.value = newVal
})

const handleClose = () => {
    openModel.value = false;
    emit('dismiss', false)
}

const handleSubmit = () => {
    emit('submit')
}

</script>
