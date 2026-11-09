import http from '@/api/http';

export const shopService = {
    list: (params) => http.get('/admin/shops', { params }).then((res) => res.data),
    create: (payload) => http.post('/admin/shops', payload).then((res) => res.data),
    update: (id, payload) => http.put(`/admin/shops/${id}`, payload).then((res) => res.data),
    remove: (id) => http.delete(`/admin/shops/${id}`),
};
