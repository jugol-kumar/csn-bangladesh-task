import http from '@/api/http';

export const optionService = {
    owners: () => http.get('/admin/options/owners').then((res) => res.data),
    shops: () => http.get('/admin/options/shops').then((res) => res.data),
};
