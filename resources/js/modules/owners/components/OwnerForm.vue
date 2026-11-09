<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { ownerService } from '../services/owner.service';
import { optionService } from '@/modules/shared/services/option.service';
import { useFormSubmit } from '@/modules/shared/composables/useFormSubmit';
import { useToast } from '@/modules/shared/composables/useToast';
import FormField from '@/modules/shared/components/FormField.vue';

const props = defineProps({ owner: { type: Object, default: null } });
const emit = defineEmits(['saved', 'cancel']);

const toast = useToast();
const { busy, run, err } = useFormSubmit();

const form = reactive({
    name: props.owner?.name ?? '',
    email: props.owner?.email ?? '',
    password: '',
    shop_id: props.owner?.shop?.id ?? '',
});

const shops = ref([]);
const available = computed(() => shops.value.filter((shop) => !shop.owner_id || shop.owner_id === props.owner?.id));

onMounted(async () => {
    try {
        shops.value = (await optionService.shops()).data;
    } catch {
        toast.error('Could not load shops.');
    }
});

async function submit() {
    const payload = { ...form, shop_id: form.shop_id || null };
    const res = await run(() => (props.owner ? ownerService.update(props.owner.id, payload) : ownerService.create(payload)));

    if (res) {
        toast.success(props.owner ? 'Owner updated' : 'Owner created');
        emit('saved');
    }
}
</script>

<template>
    <form class="space-y-4" @submit.prevent="submit">
        <FormField label="Full name" :error="err('name')">
            <input v-model="form.name" type="text" class="input" required />
        </FormField>
        <FormField label="Email" :error="err('email')">
            <input v-model="form.email" type="email" class="input" required />
        </FormField>
        <FormField
            label="Password"
            :error="err('password')"
            :hint="owner ? 'Leave empty to keep the current password.' : 'At least 8 characters.'"
        >
            <input v-model="form.password" type="password" class="input" autocomplete="new-password" :required="!owner" />
        </FormField>
        <FormField label="Shop" :error="err('shop_id')" hint="Only shops without an owner are listed.">
            <select v-model="form.shop_id" class="input">
                <option value="">No shop yet</option>
                <option v-for="shop in available" :key="shop.id" :value="shop.id">{{ shop.name }}</option>
            </select>
        </FormField>
        <div class="flex justify-end gap-3 border-t border-dashed border-rule pt-4">
            <button type="button" class="btn btn-line" @click="emit('cancel')">Cancel</button>
            <button class="btn btn-solid" :disabled="busy">{{ busy ? 'Saving…' : owner ? 'Save changes' : 'Create owner' }}</button>
        </div>
    </form>
</template>
