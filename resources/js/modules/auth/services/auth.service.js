import http from '@/api/http';

export const authService = {
    login: (payload) => http.post('/login', payload).then((res) => res.data),
    adminLogin: (payload) => http.post('/admin/login', payload).then((res) => res.data),
    register: (payload) => http.post('/register', payload).then((res) => res.data),
    me: () => http.get('/me').then((res) => res.data),
    logout: () => http.post('/logout'),
};
