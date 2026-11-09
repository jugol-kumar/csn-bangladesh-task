<script setup>
import Thumb from '@/modules/shared/components/Thumb.vue';
import StatusTag from '@/modules/shared/components/StatusTag.vue';
import { money } from '@/utils/money';

defineProps({ items: { type: Array, required: true } });
const emit = defineEmits(['edit', 'remove']);
</script>

<template>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <article v-for="listing in items" :key="listing.id" class="panel flex flex-col p-3">
            <Thumb :url="listing.thumbnail_url" :name="listing.title ?? ''" />
            <div class="mt-3 flex-1">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="font-medium">{{ listing.title }}</h3>
                    <StatusTag :on="listing.is_active" on-label="Visible" off-label="Hidden" />
                </div>
                <p class="mt-0.5 line-clamp-2 text-xs text-mute">{{ listing.description }}</p>
                <p class="mt-2 font-mono text-lg font-semibold tabular-nums">{{ money(listing.price) }}</p>
                <p v-if="listing.product && listing.product.base_price !== listing.price" class="font-mono text-[11px] text-mute">
                    catalog {{ money(listing.product.base_price) }}
                </p>
            </div>
            <div class="mt-3 flex gap-2 border-t border-dashed border-rule pt-3">
                <button type="button" class="btn btn-line btn-sm" @click="emit('edit', listing)">Edit</button>
                <button type="button" class="btn btn-danger btn-sm" @click="emit('remove', listing)">Remove</button>
            </div>
        </article>
    </div>
</template>
