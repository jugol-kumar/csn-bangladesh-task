<script setup>
defineProps({ to: { type: String, default: '/' } });
</script>

<template>
    <RouterLink :to="to" class="inline-flex items-center gap-2">
        <span class="block h-3.5 w-3.5 bg-brick"></span>
        <span class="font-mono text-xl font-semibold tracking-tight">haat</span>
    </RouterLink>
</template>
