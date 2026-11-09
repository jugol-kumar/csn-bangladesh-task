<script setup>
defineProps({ shop: { type: Object, required: true } });
</script>

<template>
    <RouterLink
        :to="`/shops/${shop.slug}`"
        class="group flex min-h-44 flex-col justify-between border-2 border-ink bg-card p-5 transition-colors hover:bg-ink hover:text-paper"
    >
        <div>
            <h3 class="text-xl font-semibold tracking-tight">{{ shop.name }}</h3>
            <p v-if="shop.address" class="mt-0.5 font-mono text-xs text-mute group-hover:text-paper/70">{{ shop.address }}</p>
            <p v-if="shop.description" class="mt-3 line-clamp-2 text-sm text-mute group-hover:text-paper/70">{{ shop.description }}</p>
        </div>
        <div class="mt-4 flex items-center justify-between font-mono text-xs tracking-wider uppercase">
            <span>{{ shop.listings_count ?? 0 }} products</span>
            <span class="transition-transform group-hover:translate-x-1">Open →</span>
        </div>
    </RouterLink>
</template>
