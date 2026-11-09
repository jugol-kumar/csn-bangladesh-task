<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import StoreLayout from '@/layouts/StoreLayout.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import BareLayout from '@/layouts/BareLayout.vue';
import ToastHost from '@/modules/shared/components/ToastHost.vue';

const route = useRoute();

const layouts = {
    store: StoreLayout,
    admin: AdminLayout,
    owner: OwnerLayout,
    bare: BareLayout,
};

const layout = computed(() => layouts[route.meta.layout] ?? StoreLayout);
</script>

<template>
    <component :is="layout">
        <RouterView />
    </component>
    <ToastHost />
</template>
