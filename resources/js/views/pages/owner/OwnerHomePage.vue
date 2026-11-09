<script setup>
import { computed, onMounted, ref } from 'vue';
import { dashboardService } from '@/modules/dashboard/services/dashboard.service';
import { useToast } from '@/modules/shared/composables/useToast';
import PageHeading from '@/modules/shared/components/PageHeading.vue';
import EmptyState from '@/modules/shared/components/EmptyState.vue';
import CounterStrip from '@/modules/dashboard/components/CounterStrip.vue';
import ModuleBoxes from '@/modules/dashboard/components/ModuleBoxes.vue';

const toast = useToast();
const data = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        data.value = await dashboardService.owner();
    } catch {
        toast.error('Could not load your overview.');
    } finally {
        loading.value = false;
    }
});

const strip = computed(() => [
    { label: 'In my shop', value: data.value?.counters?.listings },
    { label: 'Visible', value: data.value?.counters?.active, note: 'customers can see these' },
    { label: 'Catalog size', value: data.value?.counters?.catalog },
    { label: 'Not in my shop', value: data.value?.counters?.available, note: 'ready to add' },
    { label: 'Created by me', value: data.value?.counters?.mine },
]);

const modules = [
    { no: '01', title: 'Catalog', text: 'Browse every product and add the ones you sell.', to: '/owner/catalog' },
    { no: '02', title: 'My products', text: 'Change prices, details and thumbnails for your shop.', to: '/owner/listings' },
    { no: '03', title: 'New product', text: 'Add something new to the shared catalog and your shelf.', to: '/owner/products/new' },
];
</script>

<template>
    <div>
        <p v-if="loading" class="font-mono text-xs text-mute">Loading…</p>

        <EmptyState
            v-else-if="data && !data.shop"
            title="No shop linked yet"
            text="An admin has to link a shop to your account before you can list products. Check back soon."
        />

        <template v-else-if="data">
            <PageHeading eyebrow="Your shop" :title="data.shop.name">
                <RouterLink :to="`/shops/${data.shop.slug}`" class="btn btn-line">View as customer</RouterLink>
            </PageHeading>

            <section>
                <h2 class="label">Right now</h2>
                <CounterStrip :items="strip" :loading="loading" />
            </section>

            <section class="mt-10">
                <h2 class="label">Work on</h2>
                <ModuleBoxes :modules="modules" />
            </section>
        </template>
    </div>
</template>
