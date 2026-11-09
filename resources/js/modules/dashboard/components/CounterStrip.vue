<script setup>
defineProps({
    items: { type: Array, required: true },
    loading: Boolean,
});
</script>

<template>
    <dl class="grid grid-cols-[repeat(auto-fit,minmax(10rem,1fr))] gap-3">
        <div v-for="item in items" :key="item.label" class="panel p-4">
            <dt class="font-mono text-[11px] tracking-wider text-mute uppercase">{{ item.label }}</dt>
            <dd class="mt-2 font-mono text-4xl font-semibold tabular-nums">{{ loading ? '–' : item.value }}</dd>
            <p v-if="item.note" class="mt-1 text-xs text-mute">{{ item.note }}</p>
        </div>
    </dl>
</template>
