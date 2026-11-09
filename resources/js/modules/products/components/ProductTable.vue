<script setup>
import Thumb from '@/modules/shared/components/Thumb.vue';
import { money } from '@/utils/money';

defineProps({ items: { type: Array, required: true } });
const emit = defineEmits(['edit', 'remove']);
</script>

<template>
    <div class="panel overflow-x-auto">
        <table class="tbl min-w-[48rem]">
            <thead>
                <tr>
                    <th class="w-20"></th>
                    <th>Product</th>
                    <th class="text-right">Base price</th>
                    <th>Added by</th>
                    <th class="text-right">In shops</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="product in items" :key="product.id">
                    <td><Thumb :url="product.thumbnail_url" :name="product.name" square /></td>
                    <td>
                        <p class="font-medium">{{ product.name }}</p>
                        <p class="line-clamp-1 max-w-xs text-xs text-mute">{{ product.description }}</p>
                    </td>
                    <td class="text-right font-mono tabular-nums">{{ money(product.base_price) }}</td>
                    <td>
                        <span class="tag" :class="product.source === 'admin' ? 'border-ink' : 'border-brick text-brick'">
                            {{ product.source === 'admin' ? 'Admin' : 'Owner' }}
                        </span>
                        <p v-if="product.creator" class="mt-1 text-xs text-mute">{{ product.creator.name }}</p>
                    </td>
                    <td class="text-right font-mono tabular-nums">
                        <span :class="product.listings_count ? '' : 'text-brick'">{{ product.listings_count ?? 0 }}</span>
                        <p v-if="!product.listings_count" class="font-sans text-xs text-brick">hidden</p>
                    </td>
                    <td>
                        <div class="flex justify-end gap-2">
                            <button type="button" class="btn btn-line btn-sm" @click="emit('edit', product)">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm" @click="emit('remove', product)">Delete</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
