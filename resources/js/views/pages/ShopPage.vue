<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { usePagedList } from '@/modules/shared/composables/usePagedList';
import { storefrontService } from '@/modules/storefront/services/storefront.service';
import SearchBar from '@/modules/shared/components/SearchBar.vue';
import Pagination from '@/modules/shared/components/Pagination.vue';
import EmptyState from '@/modules/shared/components/EmptyState.vue';
import ShopHeader from '@/modules/storefront/components/ShopHeader.vue';
import ProductCard from '@/modules/storefront/components/ProductCard.vue';

const route = useRoute();
const slug = computed(() => route.params.slug);
const shop = ref(null);
const missing = ref(false);
const filters = reactive({ sort: '' });

const { items, meta, loading, query, load, setPage } = usePagedList(
    (params) => storefrontService.products(slug.value, params),
    filters
);

async function init() {
    missing.value = false;
    shop.value = null;

    try {
        shop.value = (await storefrontService.shop(slug.value)).data;
        await load();
    } catch (error) {
        if (error.response?.status === 404) missing.value = true;
    }
}

onMounted(init);

watch(slug, (value) => {
    if (value) {
        query.search = '';
        query.page = 1;
        init();
    }
});
</script>

<template>
    <EmptyState v-if="missing" title="Shop not found" text="This shop is closed or the address is wrong.">
        <RouterLink to="/" class="btn btn-solid">Back to shops</RouterLink>
    </EmptyState>

    <div v-else-if="shop">
        <ShopHeader :shop="shop" />

        <div class="mb-5 flex flex-wrap gap-3">
            <div class="min-w-56 flex-1">
                <SearchBar v-model="query.search" placeholder="Search this shop" />
            </div>
            <select v-model="filters.sort" class="input w-auto">
                <option value="">Newest first</option>
                <option value="price_asc">Price, low to high</option>
                <option value="price_desc">Price, high to low</option>
            </select>
        </div>

        <p v-if="loading" class="mb-3 font-mono text-xs text-mute">Loading products…</p>
        <div v-if="items.length" class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <ProductCard v-for="listing in items" :key="listing.id" :listing="listing" />
        </div>
        <EmptyState v-else-if="!loading" title="Nothing here" text="No products match your search." />
        <Pagination :meta="meta" @change="setPage" />
    </div>
</template>
