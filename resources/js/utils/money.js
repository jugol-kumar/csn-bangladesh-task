const symbol = import.meta.env.VITE_CURRENCY ?? '৳';
const formatter = new Intl.NumberFormat('en-IN', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

export function money(value) {
    return `${symbol}${formatter.format(Number(value ?? 0))}`;
}
