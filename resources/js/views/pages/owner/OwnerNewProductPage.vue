<script setup>
import { useRouter } from 'vue-router';
import { catalogService } from '@/modules/catalog/services/catalog.service';
import PageHeading from '@/modules/shared/components/PageHeading.vue';
import ProductForm from '@/modules/products/components/ProductForm.vue';

const router = useRouter();

function saver(payload) {
    return catalogService.createProduct(payload);
}

function done() {
    router.push('/owner/listings');
}
</script>

<template>
    <div>
        <PageHeading eyebrow="Shared catalog" title="New product" back="/owner" back-label="Overview" />

        <div class="panel max-w-2xl p-6">
            <p class="mb-5 border-b border-dashed border-rule pb-4 text-sm text-mute">
                The product joins the shared catalog, so other shop owners can add it too. It goes on your shelf right away at the base price.
            </p>
            <ProductForm :saver="saver" submit-label="Create and add to my shop" @saved="done" @cancel="router.push('/owner/catalog')" />
        </div>
    </div>
</template>
