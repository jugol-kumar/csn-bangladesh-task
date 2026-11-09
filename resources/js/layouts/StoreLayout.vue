<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import BrandMark from '@/modules/shared/components/BrandMark.vue';

const auth = useAuthStore();
const router = useRouter();

const desk = computed(() => {
    if (auth.isAdmin) return { to: '/admin', label: 'Admin' };
    if (auth.isOwner) return { to: '/owner', label: 'My shop' };
    return null;
});

async function logout() {
    await auth.logout();
    router.push('/');
}
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <header class="border-b-2 border-ink">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4">
                <BrandMark />
                <nav class="flex items-center gap-5 font-mono text-xs tracking-wider uppercase">
                    <RouterLink to="/" class="hover:text-brick">Shops</RouterLink>
                    <RouterLink v-if="desk" :to="desk.to" class="hover:text-brick">{{ desk.label }}</RouterLink>
                    <template v-if="auth.isAuthenticated">
                        <span class="hidden text-mute sm:inline">{{ auth.user.name }}</span>
                        <button type="button" class="tracking-wider uppercase hover:text-brick" @click="logout">Logout</button>
                    </template>
                    <template v-else>
                        <RouterLink to="/login" class="hover:text-brick">Login</RouterLink>
                        <RouterLink to="/register" class="btn btn-solid btn-sm">Register</RouterLink>
                    </template>
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
            <slot />
        </main>

        <footer class="border-t border-dashed border-rule">
            <div class="mx-auto max-w-6xl px-4 py-5 font-mono text-xs text-mute">haat · one shared catalog, every shop sets its own price</div>
        </footer>
    </div>
</template>
