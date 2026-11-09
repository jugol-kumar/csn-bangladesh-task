import { ref } from 'vue';
import { useToast } from './useToast';

export function useFormSubmit() {
    const busy = ref(false);
    const errors = ref({});
    const toast = useToast();

    async function run(action) {
        busy.value = true;
        errors.value = {};

        try {
            return await action();
        } catch (error) {
            const body = error.response?.data;
            errors.value = body?.errors ?? {};

            if (!body?.errors) {
                toast.error(body?.message ?? 'Something went wrong. Please try again.');
            }

            return undefined;
        } finally {
            busy.value = false;
        }
    }

    const err = (key) => errors.value[key]?.[0];

    return { busy, errors, run, err };
}
