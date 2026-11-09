<script setup>
import { shopService } from '@/modules/shops/services/shop.service';
import { useCrudPage } from '@/modules/shared/composables/useCrudPage';
import PageHeading from '@/modules/shared/components/PageHeading.vue';
import SearchBar from '@/modules/shared/components/SearchBar.vue';
import Pagination from '@/modules/shared/components/Pagination.vue';
import EmptyState from '@/modules/shared/components/EmptyState.vue';
import BaseModal from '@/modules/shared/components/BaseModal.vue';
import ConfirmDialog from '@/modules/shared/components/ConfirmDialog.vue';
import ShopTable from '@/modules/shops/components/ShopTable.vue';
import ShopForm from '@/modules/shops/components/ShopForm.vue';

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
} = useCrudPage({ list: shopService.list, remove: shopService.remove, noun: 'Shop' });
</script>

<template>
    <div>
        <PageHeading eyebrow="Module 01" title="Shops" back="/admin" back-label="Admin home">
            <button type="button" class="btn btn-solid" @click="openForm()">New shop</button>
        </PageHeading>

        <div class="mb-4 max-w-sm">
            <SearchBar v-model="query.search" placeholder="Search shops" />
        </div>

        <p v-if="loading" class="mb-3 font-mono text-xs text-mute">Loading…</p>
        <ShopTable v-if="items.length" :items="items" @edit="openForm" @remove="askRemove" />
        <EmptyState v-else-if="!loading" title="No shops yet" text="Create the first shop, then link an owner to it.">
            <button type="button" class="btn btn-solid" @click="openForm()">New shop</button>
        </EmptyState>
        <Pagination :meta="meta" @change="setPage" />

        <BaseModal :open="modal.open" :title="modal.record ? 'Edit shop' : 'New shop'" @close="closeForm">
            <ShopForm :shop="modal.record" @saved="saved" @cancel="closeForm" />
        </BaseModal>

        <ConfirmDialog
            :open="confirm.open"
            title="Delete shop"
            :message="`Delete ${confirm.record?.name}? Its product listings are deleted with it. Catalog products stay.`"
            :busy="confirm.busy"
            @confirm="confirmRemove"
            @cancel="cancelRemove"
        />
    </div>
</template>
