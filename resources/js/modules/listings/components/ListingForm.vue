<script setup>
import { reactive } from 'vue';
import { listingService } from '../services/listing.service';
import { useFormSubmit } from '@/modules/shared/composables/useFormSubmit';
import { useToast } from '@/modules/shared/composables/useToast';
import FormField from '@/modules/shared/components/FormField.vue';
import ImagePicker from '@/modules/shared/components/ImagePicker.vue';

const props = defineProps({ listing: { type: Object, required: true } });
const emit = defineEmits(['saved', 'cancel']);

const toast = useToast();
const { busy, run, err } = useFormSubmit();

const form = reactive({
    price: props.listing.price,
    title: props.listing.title ?? '',
    description: props.listing.description ?? '',
    is_active: props.listing.is_active,
    thumbnail: null,
    remove_thumbnail: false,
});

async function submit() {
    const res = await run(() => listingService.update(props.listing.id, { ...form }));

    if (res) {
        toast.success('Product updated');
        emit('saved');
    }
}
</script>

<template>
    <form class="space-y-4" @submit.prevent="submit">
        <FormField label="Your price" :error="err('price')">
            <input v-model="form.price" type="number" min="0" step="0.01" class="input font-mono" required />
        </FormField>
        <FormField label="Shop title" :error="err('title')">
            <input v-model="form.title" type="text" class="input" />
        </FormField>
        <FormField label="Shop description" :error="err('description')">
            <textarea v-model="form.description" rows="4" class="input"></textarea>
        </FormField>
        <ImagePicker
            v-model="form.thumbnail"
            v-model:remove="form.remove_thumbnail"
            label="Shop thumbnail"
            hint="Remove it to fall back to the catalog thumbnail."
            :current-url="listing.own_thumbnail_url"
            :name="form.title"
            :error="err('thumbnail')"
        />
        <label class="flex items-center gap-2 text-sm">
            <input v-model="form.is_active" type="checkbox" class="size-4 accent-ink" />
            Visible to customers
        </label>
        <div class="flex justify-end gap-3 border-t border-dashed border-rule pt-4">
            <button type="button" class="btn btn-line" @click="emit('cancel')">Cancel</button>
            <button class="btn btn-solid" :disabled="busy">{{ busy ? 'Saving…' : 'Save changes' }}</button>
        </div>
    </form>
</template>
