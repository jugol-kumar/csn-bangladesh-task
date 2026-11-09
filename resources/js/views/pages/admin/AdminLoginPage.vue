<script setup>
import { useRoute, useRouter } from 'vue-router';
import LoginForm from '@/modules/auth/components/LoginForm.vue';

const route = useRoute();
const router = useRouter();

function done() {
    const target = String(route.query.redirect ?? '');

    router.push(target.startsWith('/admin') ? target : '/admin');
}
</script>

<template>
    <span class="tag border-ink">Admin only</span>
    <h1 class="mt-3 text-xl font-semibold">Admin sign in</h1>
    <p class="mt-1 mb-5 text-sm text-mute">This door is separate from the shop owner and customer login.</p>
    <LoginForm mode="admin" @success="done" />
</template>
