<script setup>
import { reactive } from 'vue';
import { listingService } from '@/modules/listings/services/listing.service';
import { useFormSubmit } from '@/modules/shared/composables/useFormSubmit';
import { useToast } from '@/modules/shared/composables/useToast';
import FormField from '@/modules/shared/components/FormField.vue';
import ImagePicker from '@/modules/shared/components/ImagePicker.vue';

const props = defineProps({ product: { type: Object, required: true } });
const emit = defineEmits(['saved', 'cancel']);

const toast = useToast();
const { busy, run, err } = useFormSubmit();

const form = reactive({
    price: props.product.base_price,
    title: '',
    description: '',
    thumbnail: null,
    remove_thumbnail: false,
});

async function submit() {
    const res = await run(() => listingService.add({ ...form, product_id: props.product.id }));

    if (res) {
        toast.success(`${props.product.name} added to your shop`);
        emit('saved');
    }
}
</script>

<template>
    <form class="space-y-4" @submit.prevent="submit">
        <div class="border border-dashed border-rule p-3 text-sm">
            <p class="font-medium">{{ product.name }}</p>
            <p class="mt-0.5 text-xs text-mute">Catalog entry. Anything you leave empty below is taken from it.</p>
        </div>
        <FormField label="Your price" :error="err('price')">
            <input v-model="form.price" type="number" min="0" step="0.01" class="input font-mono" required />
        </FormField>
        <FormField label="Shop title" :error="err('title')" hint="Optional. Overrides the catalog name in your shop only.">
            <input v-model="form.title" type="text" class="input" :placeholder="product.name" />
        </FormField>
        <FormField label="Shop description" :error="err('description')" hint="Optional.">
            <textarea v-model="form.description" rows="3" class="input" :placeholder="product.description ?? ''"></textarea>
        </FormField>
        <ImagePicker
            v-model="form.thumbnail"
            v-model:remove="form.remove_thumbnail"
            label="Shop thumbnail"
            hint="Optional. Without one, the catalog thumbnail is shown."
            :name="product.name"
            :error="err('thumbnail')"
        />
        <div v-if="err('product_id')" class="text-xs font-medium text-brick">{{ err('product_id') }}</div>
        <div class="flex justify-end gap-3 border-t border-dashed border-rule pt-4">
            <button type="button" class="btn btn-line" @click="emit('cancel')">Cancel</button>
            <button class="btn btn-solid" :disabled="busy">{{ busy ? 'Adding…' : 'Add to my shop' }}</button>
        </div>
    </form>
</template>
