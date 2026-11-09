import http from '@/api/http';

export const ownerService = {
    list: (params) => http.get('/admin/owners', { params }).then((res) => res.data),
    create: (payload) => http.post('/admin/owners', payload).then((res) => res.data),
    update: (id, payload) => http.put(`/admin/owners/${id}`, payload).then((res) => res.data),
    remove: (id) => http.delete(`/admin/owners/${id}`),
};
