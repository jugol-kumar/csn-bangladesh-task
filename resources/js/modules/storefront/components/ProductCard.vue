<script setup>
import Thumb from '@/modules/shared/components/Thumb.vue';
import { money } from '@/utils/money';

defineProps({
    listing: { type: Object, required: true },
    showShop: { type: Boolean, default: false },
});
</script>

<template>
    <RouterLink
        :to="`/shops/${listing.shop.slug}/products/${listing.id}`"
        class="group flex flex-col border-2 border-ink bg-card p-3 transition-colors hover:bg-white"
    >
        <Thumb :url="listing.thumbnail_url" :name="listing.title ?? ''" />
        <div class="mt-3 flex flex-1 flex-col justify-between">
            <div>
                <h3 class="font-medium group-hover:underline">{{ listing.title }}</h3>
                <p v-if="showShop" class="mt-0.5 font-mono text-[11px] tracking-wider text-mute uppercase">{{ listing.shop.name }}</p>
            </div>
            <p class="mt-3 font-mono text-lg font-semibold tabular-nums">{{ money(listing.price) }}</p>
        </div>
    </RouterLink>
</template>
