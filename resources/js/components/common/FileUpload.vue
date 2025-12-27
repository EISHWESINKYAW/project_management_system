<template>
    <div :class="[
        'upload-wrapper flex p-4 border border-border rounded relative aspect-square',
        previewUrl ? ' justify-center items-center' : 'flex-col justify-center items-center gap-y-3', customClass ? customClass : ''
    ]">
        <div class="preview-wrapper w-full h-full" :class="customClass" v-if="previewUrl">
            <img :src="previewUrl" alt=""
                :class="[customClass ? customClass : 'rounded-xl p-1 w-full h-full object-cover']" />
        </div>
        <!-- File input -->
        <div class="input-container absolute inset-0">
            <input id="file-upload" type="file" class="absolute opacity-0 w-full h-full cursor-pointer"
                @change="handleUpload" />
        </div>
        <template v-if="!previewUrl">
            <div class="upload-icon bg-[#43465d] p-2 rounded">
                <IconUpload class="text-[#e1def5e6] text-xl" />
            </div>
            <div class="upload-message text-center">
                <p class="text-[#e1def5e6] text-2xl">Drag and drop your image here.</p>
                <p class="text-[#e1def566] text-sm">or</p>
            </div>
            <div
                class="upload-button z-10 relative bg-[#393b63] rounded transition duration-300 px-4 py-1 text-[#e1def5e6] hover:bg-[#474a77]">
                Browse Images
                <div class="input-container absolute top-0 left-0 right-0 bottom-0">
                    <input id="file-upload" type="file" class="opacity-0 w-full h-full cursor-pointer"
                        @change="handleUpload" />
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { IconUpload } from '@tabler/icons-vue';
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    showPreview: {
        type: Boolean,
        default: true,
    },
    modelValue: {
        type: String,
        default: null,
    },
    customClass: {
        type: String,
        default: '',
    }
});
const emit = defineEmits(["update:modelValue"]);

const uploadFiles = ref([]);
const previewUrl = ref(props.modelValue);
const previewType = ref(null);

watch(() => props.modelValue, (updateVal) => {
    if (typeof updateVal == 'string') {
        previewUrl.value = updateVal;
        if (previewUrl.value) {
            setPreviewType(previewUrl.value)
        }
    }
})

onMounted(() => {
    if (props.modelValue) {
        previewUrl.value = props.modelValue;
        if (previewUrl.value) {
            setPreviewType(previewUrl.value)
        }
    }
});

const setPreviewType = (preview) => {
    const fileType = preview.split('.').pop().toLowerCase();
    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
        previewType.value = 'image';
    } else if (['mp4', 'webm', 'ogg'].includes(fileType)) {
        previewType.value = 'video';
    } else {
        previewType.value = null;
    }
}

const handleUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    uploadFiles.value = e.target.files;
    emit("update:modelValue", uploadFiles.value);

    const fileType = file.type;
    if (fileType.startsWith('image/')) {
        previewType.value = 'image';
    } else if (fileType.startsWith('video/')) {
        previewType.value = 'video';
    } else {
        previewType.value = null;
    }
    previewUrl.value = URL.createObjectURL(file);
};
</script>
