import { onBeforeUnmount, reactive, ref, watch } from 'vue';
import { useToast } from './useToast';

export function usePagedList(fetcher, filters = reactive({})) {
    const toast = useToast();
    const items = ref([]);
    const meta = ref(null);
    const loading = ref(false);
    const query = reactive({ search: '', page: 1 });
    let timer = null;
    let ticket = 0;

    async function load() {
        const current = ++ticket;
        loading.value = true;

        try {
            const res = await fetcher({ ...filters, ...query });

            if (current !== ticket) return;

            if (!res.data.length && query.page > 1) {
                query.page = Math.max(1, res.meta?.last_page ?? 1);
                await load();
                return;
            }

            items.value = res.data;
            meta.value = res.meta ?? null;
        } catch (error) {
            if (current === ticket) {
                items.value = [];
                meta.value = null;
                toast.error(error.response?.data?.message ?? 'Could not load the list.');
            }
        } finally {
            if (current === ticket) loading.value = false;
        }
    }

    function setPage(page) {
        query.page = page;
        load();
    }

    watch(
        () => query.search,
        () => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                query.page = 1;
                load();
            }, 300);
        }
    );

    watch(filters, () => {
        query.page = 1;
        load();
    });

    onBeforeUnmount(() => clearTimeout(timer));

    return { items, meta, loading, query, load, setPage };
}
