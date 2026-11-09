<script setup>
defineProps({
    eyebrow: { type: String, default: '' },
    title: { type: String, required: true },
    back: { type: String, default: '' },
    backLabel: { type: String, default: 'Back' },
});
</script>

<template>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4 border-b-2 border-ink pb-4">
        <div>
            <RouterLink v-if="back" :to="back" class="mb-2 inline-block font-mono text-xs tracking-wider text-mute uppercase hover:text-brick">
                ← {{ backLabel }}
            </RouterLink>
            <p v-else-if="eyebrow" class="mb-1 font-mono text-xs tracking-wider text-mute uppercase">{{ eyebrow }}</p>
            <h1 class="text-3xl font-semibold tracking-tight">{{ title }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <slot />
        </div>
    </div>
</template>
