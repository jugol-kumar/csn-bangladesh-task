import http from '@/api/http';
import { toFormData } from '@/utils/form-data';

export const productService = {
    list: (params) => http.get('/admin/products', { params }).then((res) => res.data),
    create: (payload) => http.post('/admin/products', toFormData(payload)).then((res) => res.data),
    update: (id, payload) => http.post(`/admin/products/${id}`, toFormData(payload, 'PUT')).then((res) => res.data),
    remove: (id) => http.delete(`/admin/products/${id}`),
};
