import http from '@/api/http';

export const dashboardService = {
    admin: () => http.get('/admin/dashboard').then((res) => res.data),
    owner: () => http.get('/owner/dashboard').then((res) => res.data),
};
