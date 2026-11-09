import { defineStore } from 'pinia';
import { TOKEN_KEY } from '@/api/http';
import { authService } from '@/modules/auth/services/auth.service';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem(TOKEN_KEY),
        user: null,
        ready: false,
    }),

    getters: {
        isAuthenticated: (state) => Boolean(state.token && state.user),
        role: (state) => state.user?.role ?? null,
        isAdmin: (state) => state.user?.role === 'admin',
        isOwner: (state) => state.user?.role === 'shop_owner',
    },

    actions: {
        async init() {
            if (this.ready) return;

            if (this.token) {
                try {
                    const data = await authService.me();
                    this.user = data.user;
                } catch {
                    this.clear();
                }
            }

            this.ready = true;
        },

        async login(payload) {
            return this.start(await authService.login(payload));
        },

        async adminLogin(payload) {
            return this.start(await authService.adminLogin(payload));
        },

        async register(payload) {
            return this.start(await authService.register(payload));
        },

        start(data) {
            this.token = data.token;
            this.user = data.user;
            this.ready = true;
            localStorage.setItem(TOKEN_KEY, data.token);

            return data.user;
        },

        async logout() {
            try {
                await authService.logout();
            } catch {
                this.user = null;
            }

            this.clear();
        },

        clear() {
            this.token = null;
            this.user = null;
            localStorage.removeItem(TOKEN_KEY);
        },
    },
});
