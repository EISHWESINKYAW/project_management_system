<template>
  <TransitionRoot appear :show="openModel" as="template">
    <Dialog as="div" class="relative z-50" @close="handleClose">
      <!-- Overlay -->
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity" />
      </TransitionChild>

      <!-- Drawer Container -->
      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="fixed inset-y-0 right-0 flex max-w-full">
            <TransitionChild
              as="template"
              enter="transform transition ease-in-out duration-300"
              enter-from="translate-x-full"
              enter-to="translate-x-0"
              leave="transform transition ease-in-out duration-300"
              leave-from="translate-x-0"
              leave-to="translate-x-full"
            >
              <DialogPanel class="w-screen max-w-md h-full  bg-surface text-on-surface border border-border shadow-xl flex flex-col">
                <!-- Header -->
                <div class="px-4 py-4 border-b">
                  <DialogTitle as="h3" class="text-base font-semibold">
                    {{ title }}
                  </DialogTitle>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto px-4 py-5">
                  <div :class="modelBodyClass">
                    <slot />
                  </div>
                </div>

                <!-- Footer -->
                <div class="border-t px-4 py-4 flex justify-end gap-x-3">
                  <SmallButton
                    name="Cancel"
                    customClasses="bg-warning text-on-warning rounded py-1.5"
                    @click="handleClose"
                  />
                  <SmallButton
                    name="Submit"
                    customClasses="bg-primary text-on-primary rounded py-1.5"
                    @click="handleSubmit"
                  />
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref, watch } from 'vue'
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot
} from '@headlessui/vue'

const props = defineProps({
  visible: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Add New User'
  },
  modelBodyClass: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['dismiss', 'submit'])

const openModel = ref(false)

watch(
  () => props.visible,
  (newVal) => {
    openModel.value = newVal
  }
)

const handleClose = () => {
  openModel.value = false
  emit('dismiss', false)
}

const handleSubmit = () => {
  emit('submit')
}
</script>
