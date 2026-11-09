<script setup>
import EmptyState from '@/modules/shared/components/EmptyState.vue';
</script>

<template>
    <EmptyState title="404 · No such page" text="The address you followed does not lead anywhere.">
        <RouterLink to="/" class="btn btn-solid">Back to shops</RouterLink>
    </EmptyState>
</template>
