export function toFormData(values, method) {
    const form = new FormData();

    Object.entries(values).forEach(([key, value]) => {
        if (value === undefined) return;

        if (value === null) {
            form.append(key, '');
        } else if (typeof value === 'boolean') {
            form.append(key, value ? '1' : '0');
        } else {
            form.append(key, value);
        }
    });

    if (method) {
        form.append('_method', method);
    }

    return form;
}
