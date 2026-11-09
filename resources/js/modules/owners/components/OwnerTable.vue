<script setup>
defineProps({ items: { type: Array, required: true } });
const emit = defineEmits(['edit', 'remove']);
</script>

<template>
    <div class="panel overflow-x-auto">
        <table class="tbl min-w-[40rem]">
            <thead>
                <tr>
                    <th>Owner</th>
                    <th>Shop</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="owner in items" :key="owner.id">
                    <td>
                        <p class="font-medium">{{ owner.name }}</p>
                        <p class="text-xs text-mute">{{ owner.email }}</p>
                    </td>
                    <td>
                        <span v-if="owner.shop">{{ owner.shop.name }}</span>
                        <span v-else class="text-brick">No shop assigned</span>
                    </td>
                    <td class="font-mono text-xs text-mute">{{ owner.created_at?.slice(0, 10) }}</td>
                    <td>
                        <div class="flex justify-end gap-2">
                            <button type="button" class="btn btn-line btn-sm" @click="emit('edit', owner)">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm" @click="emit('remove', owner)">Delete</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
