<script setup>
import Thumb from '@/modules/shared/components/Thumb.vue';
import { money } from '@/utils/money';

defineProps({ items: { type: Array, required: true } });
const emit = defineEmits(['add', 'edit', 'remove']);
</script>

<template>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <article v-for="product in items" :key="product.id" class="panel flex flex-col p-3">
            <Thumb :url="product.thumbnail_url" :name="product.name" />
            <div class="mt-3 flex-1">
                <h3 class="font-medium">{{ product.name }}</h3>
                <p class="mt-0.5 line-clamp-2 text-xs text-mute">{{ product.description }}</p>
                <p class="mt-2 font-mono text-sm tabular-nums">{{ money(product.base_price) }}</p>
                <p class="mt-1 font-mono text-[11px] tracking-wider text-mute uppercase">
                    {{ product.is_mine ? 'Added by you' : product.source === 'admin' ? 'Added by admin' : `Added by ${product.creator?.name ?? 'an owner'}` }}
                </p>
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-dashed border-rule pt-3">
                <span v-if="product.is_listed" class="tag border-moss bg-moss text-paper">In your shop</span>
                <button v-else type="button" class="btn btn-solid btn-sm" @click="emit('add', product)">Add to shop</button>
                <template v-if="product.is_mine">
                    <button type="button" class="btn btn-line btn-sm" @click="emit('edit', product)">Edit</button>
                    <button type="button" class="btn btn-danger btn-sm" @click="emit('remove', product)">Delete</button>
                </template>
            </div>
        </article>
    </div>
</template>
