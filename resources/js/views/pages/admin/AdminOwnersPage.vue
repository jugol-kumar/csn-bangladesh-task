<script setup>
import { ownerService } from '@/modules/owners/services/owner.service';
import { useCrudPage } from '@/modules/shared/composables/useCrudPage';
import PageHeading from '@/modules/shared/components/PageHeading.vue';
import SearchBar from '@/modules/shared/components/SearchBar.vue';
import Pagination from '@/modules/shared/components/Pagination.vue';
import EmptyState from '@/modules/shared/components/EmptyState.vue';
import BaseModal from '@/modules/shared/components/BaseModal.vue';
import ConfirmDialog from '@/modules/shared/components/ConfirmDialog.vue';
import OwnerTable from '@/modules/owners/components/OwnerTable.vue';
import OwnerForm from '@/modules/owners/components/OwnerForm.vue';

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
} = useCrudPage({ list: ownerService.list, remove: ownerService.remove, noun: 'Owner' });
</script>

<template>
    <div>
        <PageHeading eyebrow="Module 03" title="Shop owners" back="/admin" back-label="Admin home">
            <button type="button" class="btn btn-solid" @click="openForm()">New owner</button>
        </PageHeading>

        <div class="mb-4 max-w-sm">
            <SearchBar v-model="query.search" placeholder="Search by name or email" />
        </div>

        <p v-if="loading" class="mb-3 font-mono text-xs text-mute">Loading…</p>
        <OwnerTable v-if="items.length" :items="items" @edit="openForm" @remove="askRemove" />
        <EmptyState v-else-if="!loading" title="No shop owners" text="Create an owner account, then link it to a shop.">
            <button type="button" class="btn btn-solid" @click="openForm()">New owner</button>
        </EmptyState>
        <Pagination :meta="meta" @change="setPage" />

        <BaseModal :open="modal.open" :title="modal.record ? 'Edit owner' : 'New owner'" @close="closeForm">
            <OwnerForm :owner="modal.record" @saved="saved" @cancel="closeForm" />
        </BaseModal>

        <ConfirmDialog
            :open="confirm.open"
            title="Delete owner"
            :message="`Delete ${confirm.record?.name}? Their shop stays, but loses its owner.`"
            :busy="confirm.busy"
            @confirm="confirmRemove"
            @cancel="cancelRemove"
        />
    </div>
</template>
