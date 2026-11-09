<script setup>
import { onBeforeUnmount, onMounted, watch } from 'vue';

const props = defineProps({
    open: Boolean,
    title: { type: String, default: '' },
    wide: Boolean,
});
const emit = defineEmits(['close']);

function onKey(event) {
    if (event.key === 'Escape' && props.open) emit('close');
}

onMounted(() => window.addEventListener('keydown', onKey));

onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKey);
    document.body.style.overflow = '';
});

watch(
    () => props.open,
    (value) => {
        document.body.style.overflow = value ? 'hidden' : '';
    }
);
</script>

<template>
    <Teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-40 flex items-start justify-center overflow-y-auto bg-ink/60 p-4 sm:py-12"
            @mousedown.self="emit('close')"
        >
            <div class="w-full border-2 border-ink bg-paper" :class="wide ? 'max-w-3xl' : 'max-w-xl'" role="dialog" aria-modal="true">
                <div class="flex items-center justify-between border-b-2 border-ink px-5 py-3">
                    <h2 class="font-mono text-sm font-semibold tracking-wider uppercase">{{ title }}</h2>
                    <button type="button" class="font-mono text-xl leading-none hover:text-brick" aria-label="Close" @click="emit('close')">×</button>
                </div>
                <div class="p-5">
                    <slot />
                </div>
            </div>
        </div>
    </Teleport>
</template>
