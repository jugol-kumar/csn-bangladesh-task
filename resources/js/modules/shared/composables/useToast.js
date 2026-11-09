import { reactive } from 'vue';

const toasts = reactive([]);
let sequence = 0;

function push(type, message) {
    const id = ++sequence;
    toasts.push({ id, type, message });

    setTimeout(() => {
        const index = toasts.findIndex((toast) => toast.id === id);
        if (index >= 0) toasts.splice(index, 1);
    }, 3800);
}

export function useToast() {
    return {
        toasts,
        success: (message) => push('ok', message),
        error: (message) => push('err', message),
    };
}
