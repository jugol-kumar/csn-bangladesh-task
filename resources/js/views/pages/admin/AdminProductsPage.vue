<script setup>
import { reactive } from 'vue';
import { productService } from '@/modules/products/services/product.service';
import { useCrudPage } from '@/modules/shared/composables/useCrudPage';
import PageHeading from '@/modules/shared/components/PageHeading.vue';
import SearchBar from '@/modules/shared/components/SearchBar.vue';
import Pagination from '@/modules/shared/components/Pagination.vue';
import EmptyState from '@/modules/shared/components/EmptyState.vue';
import BaseModal from '@/modules/shared/components/BaseModal.vue';
import ConfirmDialog from '@/modules/shared/components/ConfirmDialog.vue';
import ProductTable from '@/modules/products/components/ProductTable.vue';
import ProductForm from '@/modules/products/components/ProductForm.vue';

const filters = reactive({ source: '' });

const {
    items,
    meta,
    loading,
    query,
    setPage,
    modal,
    confirm,
    openForm,
    closeForm,
    saved,
    askRemove,
    cancelRemove,
    confirmRemove,
} = useCrudPage({ list: productService.list, remove: productService.remove, noun: 'Product', filters });

function saver(payload) {
    return modal.record ? productService.update(modal.record.id, payload) : productService.create(payload);
}
</script>

<template>
    <div>
        <PageHeading eyebrow="Module 02" title="Products" back="/admin" back-label="Admin home">
            <button type="button" class="btn btn-solid" @click="openForm()">New product</button>
        </PageHeading>

        <div class="mb-4 flex flex-wrap gap-3">
            <div class="min-w-56 max-w-sm flex-1">
                <SearchBar v-model="query.search" placeholder="Search products" />
            </div>
            <select v-model="filters.source" class="input w-auto">
                <option value="">Added by anyone</option>
                <option value="admin">Added by admin</option>
                <option value="shop_owner">Added by shop owners</option>
            </select>
        </div>

        <p v-if="loading" class="mb-3 font-mono text-xs text-mute">Loading…</p>
        <ProductTable v-if="items.length" :items="items" @edit="openForm" @remove="askRemove" />
        <EmptyState v-else-if="!loading" title="No products found" text="Products added here join the shared catalog for every shop owner.">
            <button type="button" class="btn btn-solid" @click="openForm()">New product</button>
        </EmptyState>
        <Pagination :meta="meta" @change="setPage" />

        <BaseModal :open="modal.open" :title="modal.record ? 'Edit product' : 'New product'" @close="closeForm">
            <ProductForm :product="modal.record" :saver="saver" @saved="saved" @cancel="closeForm" />
        </BaseModal>

        <ConfirmDialog
            :open="confirm.open"
            title="Delete product"
            :message="`Delete ${confirm.record?.name}? It also disappears from ${confirm.record?.listings_count ?? 0} shop(s).`"
            :busy="confirm.busy"
            @confirm="confirmRemove"
            @cancel="cancelRemove"
        />
    </div>
</template>
