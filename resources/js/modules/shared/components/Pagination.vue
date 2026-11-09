<script setup>
defineProps({ meta: { type: Object, default: null } });
const emit = defineEmits(['change']);
</script>

<template>
    <div v-if="meta && meta.last_page > 1" class="mt-5 flex items-center justify-between font-mono text-xs">
        <span class="text-mute">Page {{ meta.current_page }} of {{ meta.last_page }} · {{ meta.total }} total</span>
        <div class="flex gap-2">
            <button type="button" class="btn btn-line btn-sm" :disabled="meta.current_page <= 1" @click="emit('change', meta.current_page - 1)">Prev</button>
            <button type="button" class="btn btn-line btn-sm" :disabled="meta.current_page >= meta.last_page" @click="emit('change', meta.current_page + 1)">Next</button>
        </div>
    </div>
</template>
