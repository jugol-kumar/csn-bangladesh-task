<script setup>
import { reactive } from 'vue';
import { useFormSubmit } from '@/modules/shared/composables/useFormSubmit';
import { useToast } from '@/modules/shared/composables/useToast';
import FormField from '@/modules/shared/components/FormField.vue';
import ImagePicker from '@/modules/shared/components/ImagePicker.vue';

const props = defineProps({
    product: { type: Object, default: null },
    saver: { type: Function, required: true },
    submitLabel: { type: String, default: '' },
});
const emit = defineEmits(['saved', 'cancel']);

const toast = useToast();
const { busy, run, err } = useFormSubmit();

const form = reactive({
    name: props.product?.name ?? '',
    description: props.product?.description ?? '',
    base_price: props.product?.base_price ?? '',
    thumbnail: null,
    remove_thumbnail: false,
});

async function submit() {
    const res = await run(() => props.saver({ ...form }));

    if (res) {
        toast.success(props.product ? 'Product updated' : 'Product created');
        emit('saved', res);
    }
}
</script>

<template>
    <form class="space-y-4" @submit.prevent="submit">
        <FormField label="Product name" :error="err('name')">
            <input v-model="form.name" type="text" class="input" required />
        </FormField>
        <FormField label="Base price" :error="err('base_price')" hint="Shops start from this price and can change it for themselves.">
            <input v-model="form.base_price" type="number" min="0" step="0.01" class="input font-mono" required />
        </FormField>
        <FormField label="Description" :error="err('description')">
            <textarea v-model="form.description" rows="4" class="input"></textarea>
        </FormField>
        <ImagePicker
            v-model="form.thumbnail"
            v-model:remove="form.remove_thumbnail"
            :current-url="product?.thumbnail_url"
            :name="form.name"
            :error="err('thumbnail')"
        />
        <div class="flex justify-end gap-3 border-t border-dashed border-rule pt-4">
            <button type="button" class="btn btn-line" @click="emit('cancel')">Cancel</button>
            <button class="btn btn-solid" :disabled="busy">
                {{ busy ? 'Saving…' : submitLabel || (product ? 'Save changes' : 'Create product') }}
            </button>
        </div>
    </form>
</template>
