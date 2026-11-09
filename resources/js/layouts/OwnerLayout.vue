<script setup>
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import BrandMark from '@/modules/shared/components/BrandMark.vue';

const auth = useAuthStore();
const route = useRoute();
const router = useRouter();

const tabs = [
    { to: '/owner', label: 'Overview', exact: true },
    { to: '/owner/catalog', label: 'Catalog' },
    { to: '/owner/listings', label: 'My products' },
    { to: '/owner/products/new', label: 'New product' },
];

function isActive(tab) {
    return tab.exact ? route.path === tab.to : route.path.startsWith(tab.to);
}

async function logout() {
    await auth.logout();
    router.push('/login');
}
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <header class="border-b-2 border-ink">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4">
                <div class="flex items-center gap-3">
                    <BrandMark to="/owner" />
                    <span class="tag border-ink">Shop desk</span>
                </div>
                <nav class="flex items-center gap-5 font-mono text-xs tracking-wider uppercase">
                    <RouterLink to="/" class="hover:text-brick">Storefront</RouterLink>
                    <span class="hidden text-mute sm:inline">{{ auth.user?.name }}</span>
                    <button type="button" class="tracking-wider uppercase hover:text-brick" @click="logout">Logout</button>
                </nav>
            </div>
            <div class="mx-auto max-w-6xl overflow-x-auto px-4">
                <div class="flex w-max border-x border-t border-ink font-mono text-xs tracking-wider uppercase">
                    <RouterLink
                        v-for="tab in tabs"
                        :key="tab.to"
                        :to="tab.to"
                        class="border-r border-ink px-4 py-2.5 last:border-r-0"
                        :class="isActive(tab) ? 'bg-ink text-paper' : 'hover:bg-card'"
                    >
                        {{ tab.label }}
                    </RouterLink>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
            <slot />
        </main>
    </div>
</template>
