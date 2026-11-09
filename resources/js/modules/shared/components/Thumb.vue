<script setup>
import { computed } from 'vue';

const props = defineProps({
    url: { type: String, default: null },
    name: { type: String, default: '' },
    square: Boolean,
});

const initial = computed(() => (props.name.trim()[0] ?? '·').toUpperCase());
</script>

<template>
    <div class="w-full overflow-hidden border border-ink bg-card" :class="square ? 'aspect-square' : 'aspect-[4/3]'">
        <img v-if="url" :src="url" :alt="name" class="h-full w-full object-cover" loading="lazy" />
        <div v-else class="hatch flex h-full w-full items-center justify-center font-mono text-3xl font-semibold text-ink/40">
            {{ initial }}
        </div>
    </div>
</template>
