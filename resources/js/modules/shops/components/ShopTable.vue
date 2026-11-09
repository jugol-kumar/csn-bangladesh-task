<script setup>
import StatusTag from '@/modules/shared/components/StatusTag.vue';

defineProps({ items: { type: Array, required: true } });
const emit = defineEmits(['edit', 'remove']);
</script>

<template>
    <div class="panel overflow-x-auto">
        <table class="tbl min-w-[44rem]">
            <thead>
                <tr>
                    <th>Shop</th>
                    <th>Owner</th>
                    <th class="text-right">Products</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="shop in items" :key="shop.id">
                    <td>
                        <p class="font-medium">{{ shop.name }}</p>
                        <p class="font-mono text-xs text-mute">/{{ shop.slug }}</p>
                    </td>
                    <td>
                        <template v-if="shop.owner">
                            <p>{{ shop.owner.name }}</p>
                            <p class="text-xs text-mute">{{ shop.owner.email }}</p>
                        </template>
                        <span v-else class="text-mute">Unassigned</span>
                    </td>
                    <td class="text-right font-mono tabular-nums">{{ shop.listings_count ?? 0 }}</td>
                    <td><StatusTag :on="shop.is_active" on-label="Open" off-label="Closed" /></td>
                    <td>
                        <div class="flex justify-end gap-2">
                            <button type="button" class="btn btn-line btn-sm" @click="emit('edit', shop)">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm" @click="emit('remove', shop)">Delete</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
