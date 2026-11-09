<script setup>
defineProps({ shop: { type: Object, required: true } });
</script>

<template>
    <div class="mb-6 border-b-2 border-ink pb-5">
        <RouterLink to="/" class="mb-2 inline-block font-mono text-xs tracking-wider text-mute uppercase hover:text-brick">← All shops</RouterLink>
        <h1 class="text-4xl font-semibold tracking-tight">{{ shop.name }}</h1>
        <p v-if="shop.description" class="mt-2 max-w-2xl text-mute">{{ shop.description }}</p>
        <dl class="mt-3 flex flex-wrap gap-x-6 gap-y-1 font-mono text-xs">
            <div v-if="shop.address"><dt class="inline text-mute">Where </dt><dd class="inline">{{ shop.address }}</dd></div>
            <div v-if="shop.phone"><dt class="inline text-mute">Call </dt><dd class="inline">{{ shop.phone }}</dd></div>
            <div><dt class="inline text-mute">Products </dt><dd class="inline">{{ shop.listings_count ?? 0 }}</dd></div>
        </dl>
    </div>
</template>
