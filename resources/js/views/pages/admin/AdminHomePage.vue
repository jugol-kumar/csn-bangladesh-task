<script setup>
import { computed, onMounted, ref } from 'vue';
import { dashboardService } from '@/modules/dashboard/services/dashboard.service';
import { useToast } from '@/modules/shared/composables/useToast';
import PageHeading from '@/modules/shared/components/PageHeading.vue';
import CounterStrip from '@/modules/dashboard/components/CounterStrip.vue';
import ModuleBoxes from '@/modules/dashboard/components/ModuleBoxes.vue';

const toast = useToast();
const counters = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        counters.value = (await dashboardService.admin()).counters;
    } catch {
        toast.error('Could not load the counters.');
    } finally {
        loading.value = false;
    }
});

const strip = computed(() => [
    { label: 'Shops', value: counters.value?.shops },
    { label: 'Shop owners', value: counters.value?.owners },
    { label: 'Products', value: counters.value?.products },
    { label: 'Shop listings', value: counters.value?.listings, note: 'products placed in shops' },
    { label: 'Not in a shop', value: counters.value?.unlisted, note: 'hidden from customers' },
    { label: 'Customers', value: counters.value?.customers },
]);

const modules = [
    { no: '01', title: 'Shops', text: 'Create, edit and close shops. Link an owner to each.', to: '/admin/shops' },
    { no: '02', title: 'Products', text: 'Manage the shared catalog that every shop picks from.', to: '/admin/products' },
    { no: '03', title: 'Shop owners', text: 'Create owner accounts and connect them to shops.', to: '/admin/owners' },
];
</script>

<template>
    <div>
        <PageHeading eyebrow="Control room" title="Admin home" />

        <section>
            <h2 class="label">Right now</h2>
            <CounterStrip :items="strip" :loading="loading" />
        </section>

        <section class="mt-10">
            <h2 class="label">Manage</h2>
            <ModuleBoxes :modules="modules" />
        </section>
    </div>
</template>
