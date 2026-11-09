<script setup>
import Thumb from '@/modules/shared/components/Thumb.vue';
import { money } from '@/utils/money';

defineProps({ listing: { type: Object, required: true } });
</script>

<template>
    <div class="grid gap-8 md:grid-cols-[minmax(0,26rem)_1fr]">
        <Thumb :url="listing.thumbnail_url" :name="listing.title ?? ''" />
        <div>
            <RouterLink
                v-if="listing.shop"
                :to="`/shops/${listing.shop.slug}`"
                class="font-mono text-xs tracking-wider text-mute uppercase hover:text-brick"
            >
                {{ listing.shop.name }}
            </RouterLink>
            <h1 class="mt-1 text-4xl font-semibold tracking-tight">{{ listing.title }}</h1>
            <p class="mt-4 font-mono text-3xl font-semibold tabular-nums">{{ money(listing.price) }}</p>
            <div class="mt-6 border-t border-dashed border-rule pt-5">
                <p class="label">Details</p>
                <p class="max-w-prose whitespace-pre-line">{{ listing.description || 'No description added yet.' }}</p>
            </div>
            <dl v-if="listing.shop?.address || listing.shop?.phone" class="mt-6 space-y-1 font-mono text-xs">
                <div v-if="listing.shop.address"><dt class="inline text-mute">Pick up at </dt><dd class="inline">{{ listing.shop.address }}</dd></div>
                <div v-if="listing.shop.phone"><dt class="inline text-mute">Call </dt><dd class="inline">{{ listing.shop.phone }}</dd></div>
            </dl>
        </div>
    </div>
</template>
