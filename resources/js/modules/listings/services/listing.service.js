import http from '@/api/http';
import { toFormData } from '@/utils/form-data';

export const listingService = {
    list: (params) => http.get('/owner/listings', { params }).then((res) => res.data),
    add: (payload) => http.post('/owner/listings', toFormData(payload)).then((res) => res.data),
    update: (id, payload) => http.post(`/owner/listings/${id}`, toFormData(payload, 'PUT')).then((res) => res.data),
    remove: (id) => http.delete(`/owner/listings/${id}`),
};
