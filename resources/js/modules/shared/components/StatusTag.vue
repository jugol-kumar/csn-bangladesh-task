<script setup>
defineProps({
    on: Boolean,
    onLabel: { type: String, default: 'Active' },
    offLabel: { type: String, default: 'Hidden' },
});
</script>

<template>
    <span class="tag" :class="on ? 'border-moss bg-moss text-paper' : 'border-rule text-mute'">
        {{ on ? onLabel : offLabel }}
    </span>
</template>
