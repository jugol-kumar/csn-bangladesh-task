import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const routes = [
    { path: '/', component: () => import('@/views/pages/HomePage.vue'), meta: { layout: 'store' } },
    { path: '/shops/:slug', component: () => import('@/views/pages/ShopPage.vue'), meta: { layout: 'store' } },
    { path: '/shops/:slug/products/:id', component: () => import('@/views/pages/ProductPage.vue'), meta: { layout: 'store' } },

    { path: '/login', component: () => import('@/views/pages/LoginPage.vue'), meta: { layout: 'bare', guest: true } },
    { path: '/register', component: () => import('@/views/pages/RegisterPage.vue'), meta: { layout: 'bare', guest: true } },

    { path: '/admin/login', component: () => import('@/views/pages/admin/AdminLoginPage.vue'), meta: { layout: 'bare', guest: true } },
    { path: '/admin', component: () => import('@/views/pages/admin/AdminHomePage.vue'), meta: { layout: 'admin', roles: ['admin'] } },
    { path: '/admin/shops', component: () => import('@/views/pages/admin/AdminShopsPage.vue'), meta: { layout: 'admin', roles: ['admin'] } },
    { path: '/admin/products', component: () => import('@/views/pages/admin/AdminProductsPage.vue'), meta: { layout: 'admin', roles: ['admin'] } },
    { path: '/admin/owners', component: () => import('@/views/pages/admin/AdminOwnersPage.vue'), meta: { layout: 'admin', roles: ['admin'] } },

    { path: '/owner', component: () => import('@/views/pages/owner/OwnerHomePage.vue'), meta: { layout: 'owner', roles: ['shop_owner'] } },
    { path: '/owner/catalog', component: () => import('@/views/pages/owner/OwnerCatalogPage.vue'), meta: { layout: 'owner', roles: ['shop_owner'] } },
    { path: '/owner/listings', component: () => import('@/views/pages/owner/OwnerListingsPage.vue'), meta: { layout: 'owner', roles: ['shop_owner'] } },
    { path: '/owner/products/new', component: () => import('@/views/pages/owner/OwnerNewProductPage.vue'), meta: { layout: 'owner', roles: ['shop_owner'] } },

    { path: '/:pathMatch(.*)*', component: () => import('@/views/pages/NotFoundPage.vue'), meta: { layout: 'store' } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior: () => ({ top: 0 }),
});

export function homeFor(role) {
    if (role === 'admin') return '/admin';
    if (role === 'shop_owner') return '/owner';
    return '/';
}

router.beforeEach(async (to) => {
    const auth = useAuthStore();
    await auth.init();

    if (to.meta.roles) {
        if (!auth.isAuthenticated) {
            const login = to.path.startsWith('/admin') ? '/admin/login' : '/login';
            return { path: login, query: { redirect: to.fullPath } };
        }

        if (!to.meta.roles.includes(auth.role)) {
            return homeFor(auth.role);
        }
    }

    if (to.meta.guest && auth.isAuthenticated) {
        return homeFor(auth.role);
    }

    return true;
});

export default router;
