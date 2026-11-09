<script setup>
defineProps({
    label: { type: String, required: true },
    error: { type: String, default: '' },
    hint: { type: String, default: '' },
});
</script>

<template>
    <label class="block">
        <span class="label">{{ label }}</span>
        <slot />
        <span v-if="hint && !error" class="mt-1 block text-xs text-mute">{{ hint }}</span>
        <span v-if="error" class="mt-1 block text-xs font-medium text-brick">{{ error }}</span>
    </label>
</template>
