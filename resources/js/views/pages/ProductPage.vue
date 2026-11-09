<script setup>
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { storefrontService } from '@/modules/storefront/services/storefront.service';
import EmptyState from '@/modules/shared/components/EmptyState.vue';
import ProductDetail from '@/modules/storefront/components/ProductDetail.vue';

const route = useRoute();
const listing = ref(null);
const missing = ref(false);

onMounted(async () => {
    try {
        listing.value = (await storefrontService.product(route.params.slug, route.params.id)).data;
    } catch {
        missing.value = true;
    }
});
</script>

<template>
    <EmptyState v-if="missing" title="Product not found" text="It may have been taken off the shelf.">
        <RouterLink :to="`/shops/${route.params.slug}`" class="btn btn-solid">Back to the shop</RouterLink>
    </EmptyState>

    <div v-else-if="listing">
        <RouterLink
            :to="`/shops/${route.params.slug}`"
            class="mb-5 inline-block font-mono text-xs tracking-wider text-mute uppercase hover:text-brick"
        >
            ← Back to the shop
        </RouterLink>
        <ProductDetail :listing="listing" />
    </div>
</template>
