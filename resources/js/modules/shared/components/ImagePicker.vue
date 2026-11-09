<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import Thumb from './Thumb.vue';

const props = defineProps({
    modelValue: { type: Object, default: null },
    currentUrl: { type: String, default: null },
    remove: Boolean,
    label: { type: String, default: 'Thumbnail' },
    hint: { type: String, default: 'JPG, PNG or WebP, up to 2 MB.' },
    error: { type: String, default: '' },
    name: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue', 'update:remove']);

const input = ref(null);
const preview = ref(null);

watch(
    () => props.modelValue,
    (file) => {
        if (preview.value) URL.revokeObjectURL(preview.value);
        preview.value = file ? URL.createObjectURL(file) : null;
    },
    { immediate: true }
);

onBeforeUnmount(() => {
    if (preview.value) URL.revokeObjectURL(preview.value);
});

const shown = computed(() => preview.value ?? (props.remove ? null : props.currentUrl));
const hasImage = computed(() => Boolean(shown.value));

function pick(event) {
    const file = event.target.files?.[0];

    if (file) {
        emit('update:modelValue', file);
        emit('update:remove', false);
    }

    event.target.value = '';
}

function clear() {
    emit('update:modelValue', null);

    if (props.currentUrl) {
        emit('update:remove', true);
    }
}
</script>

<template>
    <div>
        <span class="label">{{ label }}</span>
        <div class="flex items-start gap-4">
            <div class="w-28 shrink-0">
                <Thumb :url="shown" :name="name" square />
            </div>
            <div class="space-y-2">
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="btn btn-line btn-sm" @click="input.click()">
                        {{ hasImage ? 'Replace' : 'Choose file' }}
                    </button>
                    <button v-if="hasImage" type="button" class="btn btn-danger btn-sm" @click="clear">Remove</button>
                </div>
                <p v-if="!error" class="text-xs text-mute">{{ hint }}</p>
                <p v-else class="text-xs font-medium text-brick">{{ error }}</p>
            </div>
        </div>
        <input ref="input" type="file" accept="image/png,image/jpeg,image/webp" class="hidden" @change="pick" />
    </div>
</template>
