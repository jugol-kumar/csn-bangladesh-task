<script setup>
defineProps({ modules: { type: Array, required: true } });
</script>

<template>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <RouterLink
            v-for="module in modules"
            :key="module.to"
            :to="module.to"
            class="group flex min-h-48 flex-col justify-between border-2 border-ink bg-card p-5 transition-colors hover:bg-ink hover:text-paper"
        >
            <div class="flex items-start justify-between">
                <span class="font-mono text-xs">{{ module.no }}</span>
                <span class="font-mono text-lg transition-transform group-hover:translate-x-1">→</span>
            </div>
            <div>
                <h3 class="text-2xl font-semibold tracking-tight">{{ module.title }}</h3>
                <p class="mt-1 text-sm text-mute group-hover:text-paper/70">{{ module.text }}</p>
            </div>
        </RouterLink>
    </div>
</template>
