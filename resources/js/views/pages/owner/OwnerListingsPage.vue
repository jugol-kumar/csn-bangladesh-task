<script setup>
import { listingService } from '@/modules/listings/services/listing.service';
import { useCrudPage } from '@/modules/shared/composables/useCrudPage';
import PageHeading from '@/modules/shared/components/PageHeading.vue';
import SearchBar from '@/modules/shared/components/SearchBar.vue';
import Pagination from '@/modules/shared/components/Pagination.vue';
import EmptyState from '@/modules/shared/components/EmptyState.vue';
import BaseModal from '@/modules/shared/components/BaseModal.vue';
import ConfirmDialog from '@/modules/shared/components/ConfirmDialog.vue';
import ListingCard from '@/modules/listings/components/ListingCard.vue';
import ListingForm from '@/modules/listings/components/ListingForm.vue';

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
} = useCrudPage({ list: listingService.list, remove: listingService.remove, noun: 'Product' });
</script>

<template>
    <div>
        <PageHeading eyebrow="Your shelf" title="My products" back="/owner" back-label="Overview">
            <RouterLink to="/owner/catalog" class="btn btn-solid">Add from catalog</RouterLink>
        </PageHeading>

        <div class="mb-5 max-w-sm">
            <SearchBar v-model="query.search" placeholder="Search your products" />
        </div>

        <p v-if="loading" class="mb-3 font-mono text-xs text-mute">Loading…</p>
        <ListingCard v-if="items.length" :items="items" @edit="openForm" @remove="askRemove" />
        <EmptyState v-else-if="!loading" title="Your shelf is empty" text="Pick products from the shared catalog to start selling.">
            <RouterLink to="/owner/catalog" class="btn btn-solid">Open the catalog</RouterLink>
        </EmptyState>
        <Pagination :meta="meta" @change="setPage" />

        <BaseModal :open="modal.open" title="Edit product in my shop" @close="closeForm">
            <ListingForm :listing="modal.record" @saved="saved" @cancel="closeForm" />
        </BaseModal>

        <ConfirmDialog
            :open="confirm.open"
            title="Remove from shop"
            :message="`Take ${confirm.record?.title} off your shelf? It stays in the shared catalog.`"
            confirm-label="Remove"
            :busy="confirm.busy"
            @confirm="confirmRemove"
            @cancel="cancelRemove"
        />
    </div>
</template>
