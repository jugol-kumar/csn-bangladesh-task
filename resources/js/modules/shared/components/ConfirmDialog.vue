<script setup>
import BaseModal from './BaseModal.vue';

defineProps({
    open: Boolean,
    title: { type: String, default: 'Please confirm' },
    message: { type: String, default: '' },
    confirmLabel: { type: String, default: 'Delete' },
    busy: Boolean,
});
const emit = defineEmits(['confirm', 'cancel']);
</script>

<template>
    <BaseModal :open="open" :title="title" @close="emit('cancel')">
        <p class="text-sm">{{ message }}</p>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" class="btn btn-line" :disabled="busy" @click="emit('cancel')">Cancel</button>
            <button type="button" class="btn btn-danger" :disabled="busy" @click="emit('confirm')">
                {{ busy ? 'Working…' : confirmLabel }}
            </button>
        </div>
    </BaseModal>
</template>
