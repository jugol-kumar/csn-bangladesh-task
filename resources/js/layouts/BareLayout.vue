<script setup>
import BrandMark from '@/modules/shared/components/BrandMark.vue';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <BrandMark class="mb-6" />
        <div class="panel w-full max-w-sm p-6">
            <slot />
        </div>
        <RouterLink to="/" class="mt-6 font-mono text-xs tracking-wider text-mute uppercase hover:text-ink">← Back to shops</RouterLink>
    </div>
</template>
