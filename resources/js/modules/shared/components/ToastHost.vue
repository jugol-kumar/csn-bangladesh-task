<script setup>
import { useToast } from '../composables/useToast';

const { toasts } = useToast();
</script>

<template>
    <div class="pointer-events-none fixed right-4 bottom-4 z-50 flex w-72 flex-col gap-2">
        <div
            v-for="toast in toasts"
            :key="toast.id"
            class="border-2 px-4 py-3 font-mono text-xs"
            :class="toast.type === 'ok' ? 'border-ink bg-ink text-paper' : 'border-brick bg-brick text-paper'"
            role="status"
        >
            {{ toast.message }}
        </div>
    </div>
</template>
