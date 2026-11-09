import http from '@/api/http';

export const storefrontService = {
    shops: (params) => http.get('/storefront/shops', { params }).then((res) => res.data),
    shop: (slug) => http.get(`/storefront/shops/${slug}`).then((res) => res.data),
    products: (slug, params) => http.get(`/storefront/shops/${slug}/products`, { params }).then((res) => res.data),
    latest: (params) => http.get('/storefront/products', { params }).then((res) => res.data),
    product: (slug, id) => http.get(`/storefront/shops/${slug}/products/${id}`).then((res) => res.data),
};
