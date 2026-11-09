<script setup>
import { onMounted, ref } from 'vue';
import { usePagedList } from '@/modules/shared/composables/usePagedList';
import { storefrontService } from '@/modules/storefront/services/storefront.service';
import SearchBar from '@/modules/shared/components/SearchBar.vue';
import Pagination from '@/modules/shared/components/Pagination.vue';
import EmptyState from '@/modules/shared/components/EmptyState.vue';
import ShopCard from '@/modules/storefront/components/ShopCard.vue';
import ProductCard from '@/modules/storefront/components/ProductCard.vue';

const {
    items: shops,
    meta: shopMeta,
    loading: shopsLoading,
    query: shopQuery,
    load: loadShops,
    setPage: setShopPage,
} = usePagedList(storefrontService.shops);

const latest = ref([]);

onMounted(async () => {
    loadShops();

    try {
        const res = await storefrontService.latest({ page: 1 });
        latest.value = res.data.slice(0, 8);
    } catch {
        latest.value = [];
    }
});
</script>

<template>
    <div>
        <section class="mb-10 grid gap-6 border-b-2 border-ink pb-8 md:grid-cols-[1fr_20rem] md:items-end">
            <div>
                <p class="mb-2 font-mono text-xs tracking-wider text-mute uppercase">Open shops</p>
                <h1 class="max-w-2xl text-4xl font-semibold tracking-tight sm:text-5xl">Pick a shop and see what is on its shelf.</h1>
            </div>
            <SearchBar v-model="shopQuery.search" placeholder="Search shops by name" />
        </section>

        <section>
            <p v-if="shopsLoading" class="mb-3 font-mono text-xs text-mute">Loading shops…</p>
            <div v-if="shops.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <ShopCard v-for="shop in shops" :key="shop.id" :shop="shop" />
            </div>
            <EmptyState v-else-if="!shopsLoading" title="No shops found" text="Shops show up here once they have products on their shelf." />
            <Pagination :meta="shopMeta" @change="setShopPage" />
        </section>

        <section v-if="latest.length" class="mt-14">
            <h2 class="mb-4 border-b border-dashed border-rule pb-2 font-mono text-xs tracking-wider text-mute uppercase">Just added to shelves</h2>
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <ProductCard v-for="listing in latest" :key="listing.id" :listing="listing" show-shop />
            </div>
        </section>
    </div>
</template>
