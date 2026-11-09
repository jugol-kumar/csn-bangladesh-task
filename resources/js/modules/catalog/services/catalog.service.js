import http from '@/api/http';
import { toFormData } from '@/utils/form-data';

export const catalogService = {
    list: (params) => http.get('/owner/catalog', { params }).then((res) => res.data),
    createProduct: (payload) => http.post('/owner/catalog', toFormData(payload)).then((res) => res.data),
    updateProduct: (id, payload) => http.post(`/owner/catalog/${id}`, toFormData(payload, 'PUT')).then((res) => res.data),
    removeProduct: (id) => http.delete(`/owner/catalog/${id}`),
};
