<script setup>
import { onMounted, reactive } from 'vue';
import { catalogService } from '@/modules/catalog/services/catalog.service';
import { usePagedList } from '@/modules/shared/composables/usePagedList';
import { useToast } from '@/modules/shared/composables/useToast';
import PageHeading from '@/modules/shared/components/PageHeading.vue';
import SearchBar from '@/modules/shared/components/SearchBar.vue';
import Pagination from '@/modules/shared/components/Pagination.vue';
import EmptyState from '@/modules/shared/components/EmptyState.vue';
import BaseModal from '@/modules/shared/components/BaseModal.vue';
import ConfirmDialog from '@/modules/shared/components/ConfirmDialog.vue';
import CatalogGrid from '@/modules/catalog/components/CatalogGrid.vue';
import AddToShopForm from '@/modules/catalog/components/AddToShopForm.vue';
import ProductForm from '@/modules/products/components/ProductForm.vue';

const toast = useToast();
const filters = reactive({ scope: 'all' });
const { items, meta, loading, query, load, setPage } = usePagedList(catalogService.list, filters);

const scopes = [
    { value: 'all', label: 'Everything' },
    { value: 'available', label: 'Not in my shop' },
    { value: 'mine', label: 'Added by me' },
];

const addModal = reactive({ open: false, product: null });
const editModal = reactive({ open: false, product: null });
const confirm = reactive({ open: false, product: null, busy: false });

onMounted(load);

function openAdd(product) {
    addModal.product = product;
    addModal.open = true;
}

function openEdit(product) {
    editModal.product = product;
    editModal.open = true;
}

function askRemove(product) {
    confirm.product = product;
    confirm.open = true;
}

async function added() {
    addModal.open = false;
    await load();
}

async function edited() {
    editModal.open = false;
    await load();
}

function saver(payload) {
    return catalogService.updateProduct(editModal.product.id, payload);
}

async function remove() {
    confirm.busy = true;

    try {
        await catalogService.removeProduct(confirm.product.id);
        toast.success('Product deleted');
        confirm.open = false;
        await load();
    } catch (error) {
        toast.error(error.response?.data?.message ?? 'Could not delete the product.');
    } finally {
        confirm.busy = false;
    }
}
</script>

<template>
    <div>
        <PageHeading eyebrow="Shared catalog" title="Catalog" back="/owner" back-label="Overview">
            <RouterLink to="/owner/products/new" class="btn btn-solid">New product</RouterLink>
        </PageHeading>

        <div class="mb-5 flex flex-wrap items-center gap-3">
            <div class="min-w-56 max-w-sm flex-1">
                <SearchBar v-model="query.search" placeholder="Search the catalog" />
            </div>
            <div class="flex border border-ink font-mono text-xs tracking-wider uppercase">
                <button
                    v-for="scope in scopes"
                    :key="scope.value"
                    type="button"
                    class="border-r border-ink px-3 py-2.5 last:border-r-0"
                    :class="filters.scope === scope.value ? 'bg-ink text-paper' : 'hover:bg-card'"
                    @click="filters.scope = scope.value"
                >
                    {{ scope.label }}
                </button>
            </div>
        </div>

        <p v-if="loading" class="mb-3 font-mono text-xs text-mute">Loading…</p>
        <CatalogGrid v-if="items.length" :items="items" @add="openAdd" @edit="openEdit" @remove="askRemove" />
        <EmptyState v-else-if="!loading" title="Nothing in this view" text="Try another filter, or add a product of your own.">
            <RouterLink to="/owner/products/new" class="btn btn-solid">New product</RouterLink>
        </EmptyState>
        <Pagination :meta="meta" @change="setPage" />

        <BaseModal :open="addModal.open" title="Add to my shop" @close="addModal.open = false">
            <AddToShopForm :product="addModal.product" @saved="added" @cancel="addModal.open = false" />
        </BaseModal>

        <BaseModal :open="editModal.open" title="Edit catalog product" @close="editModal.open = false">
            <ProductForm :product="editModal.product" :saver="saver" @saved="edited" @cancel="editModal.open = false" />
        </BaseModal>

        <ConfirmDialog
            :open="confirm.open"
            title="Delete product"
            :message="`Delete ${confirm.product?.name} from the catalog? This also removes it from your shop.`"
            :busy="confirm.busy"
            @confirm="remove"
            @cancel="confirm.open = false"
        />
    </div>
</template>
