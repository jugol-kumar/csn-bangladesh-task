import { onMounted, reactive } from 'vue';
import { usePagedList } from './usePagedList';
import { useToast } from './useToast';

export function useCrudPage({ list, remove, noun, filters }) {
    const toast = useToast();
    const paged = usePagedList(list, filters);
    const modal = reactive({ open: false, record: null });
    const confirm = reactive({ open: false, record: null, busy: false });

    function openForm(record = null) {
        modal.record = record;
        modal.open = true;
    }

    function closeForm() {
        modal.open = false;
    }

    async function saved() {
        modal.open = false;
        await paged.load();
    }

    function askRemove(record) {
        confirm.record = record;
        confirm.open = true;
    }

    function cancelRemove() {
        confirm.open = false;
    }

    async function confirmRemove() {
        confirm.busy = true;

        try {
            await remove(confirm.record.id);
            toast.success(`${noun} removed`);
            confirm.open = false;
            await paged.load();
        } catch (error) {
            toast.error(error.response?.data?.message ?? `Could not remove the ${noun.toLowerCase()}.`);
        } finally {
            confirm.busy = false;
        }
    }

    onMounted(paged.load);

    return { ...paged, modal, confirm, openForm, closeForm, saved, askRemove, cancelRemove, confirmRemove };
}
