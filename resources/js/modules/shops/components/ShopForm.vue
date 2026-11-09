<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { shopService } from '../services/shop.service';
import { optionService } from '@/modules/shared/services/option.service';
import { useFormSubmit } from '@/modules/shared/composables/useFormSubmit';
import { useToast } from '@/modules/shared/composables/useToast';
import FormField from '@/modules/shared/components/FormField.vue';

const props = defineProps({ shop: { type: Object, default: null } });
const emit = defineEmits(['saved', 'cancel']);

const toast = useToast();
const { busy, run, err } = useFormSubmit();

const form = reactive({
    name: props.shop?.name ?? '',
    description: props.shop?.description ?? '',
    address: props.shop?.address ?? '',
    phone: props.shop?.phone ?? '',
    owner_id: props.shop?.owner?.id ?? '',
    is_active: props.shop?.is_active ?? true,
});

const owners = ref([]);
const available = computed(() => owners.value.filter((owner) => !owner.shop_id || owner.shop_id === props.shop?.id));

onMounted(async () => {
    try {
        owners.value = (await optionService.owners()).data;
    } catch {
        toast.error('Could not load shop owners.');
    }
});

async function submit() {
    const payload = { ...form, owner_id: form.owner_id || null };
    const res = await run(() => (props.shop ? shopService.update(props.shop.id, payload) : shopService.create(payload)));

    if (res) {
        toast.success(props.shop ? 'Shop updated' : 'Shop created');
        emit('saved');
    }
}
</script>

<template>
    <form class="space-y-4" @submit.prevent="submit">
        <FormField label="Shop name" :error="err('name')">
            <input v-model="form.name" type="text" class="input" required />
        </FormField>
        <FormField label="Description" :error="err('description')">
            <textarea v-model="form.description" rows="3" class="input"></textarea>
        </FormField>
        <div class="grid gap-4 sm:grid-cols-2">
            <FormField label="Address" :error="err('address')">
                <input v-model="form.address" type="text" class="input" />
            </FormField>
            <FormField label="Phone" :error="err('phone')">
                <input v-model="form.phone" type="text" class="input" />
            </FormField>
        </div>
        <FormField label="Shop owner" :error="err('owner_id')" hint="Only owners without a shop are listed.">
            <select v-model="form.owner_id" class="input">
                <option value="">No owner yet</option>
                <option v-for="owner in available" :key="owner.id" :value="owner.id">{{ owner.name }} · {{ owner.email }}</option>
            </select>
        </FormField>
        <label class="flex items-center gap-2 text-sm">
            <input v-model="form.is_active" type="checkbox" class="size-4 accent-ink" />
            Shop is open to customers
        </label>
        <div class="flex justify-end gap-3 border-t border-dashed border-rule pt-4">
            <button type="button" class="btn btn-line" @click="emit('cancel')">Cancel</button>
            <button class="btn btn-solid" :disabled="busy">{{ busy ? 'Saving…' : shop ? 'Save changes' : 'Create shop' }}</button>
        </div>
    </form>
</template>
