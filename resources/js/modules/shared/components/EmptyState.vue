<script setup>
defineProps({
    title: { type: String, required: true },
    text: { type: String, default: '' },
});
</script>

<template>
    <div class="border-2 border-dashed border-rule px-6 py-12 text-center">
        <p class="font-mono text-sm font-semibold tracking-wider uppercase">{{ title }}</p>
        <p v-if="text" class="mx-auto mt-2 max-w-sm text-sm text-mute">{{ text }}</p>
        <div class="mt-4 flex justify-center">
            <slot />
        </div>
    </div>
</template>
