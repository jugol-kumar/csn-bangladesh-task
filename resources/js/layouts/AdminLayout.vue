<script setup>
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import BrandMark from '@/modules/shared/components/BrandMark.vue';

const auth = useAuthStore();
const route = useRoute();
const router = useRouter();

async function logout() {
    await auth.logout();
    router.push('/admin/login');
}
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <header class="border-b-2 border-ink bg-ink text-paper">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
                <div class="flex items-center gap-3">
                    <BrandMark to="/admin" class="[&>span:last-child]:text-paper" />
                    <span class="tag border-paper/40 text-paper/70">Admin</span>
                </div>
                <nav class="flex items-center gap-5 font-mono text-xs tracking-wider uppercase">
                    <RouterLink v-if="route.path !== '/admin'" to="/admin" class="hover:text-paper/60">Home</RouterLink>
                    <RouterLink to="/" class="hover:text-paper/60">Storefront</RouterLink>
                    <span class="hidden text-paper/60 sm:inline">{{ auth.user?.name }}</span>
                    <button type="button" class="tracking-wider uppercase hover:text-paper/60" @click="logout">Logout</button>
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
            <slot />
        </main>
    </div>
</template>
