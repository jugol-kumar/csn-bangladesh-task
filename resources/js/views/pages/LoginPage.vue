<script setup>
import { useRoute, useRouter } from 'vue-router';
import { homeFor } from '@/router';
import LoginForm from '@/modules/auth/components/LoginForm.vue';

const route = useRoute();
const router = useRouter();

function done(user) {
    const target = String(route.query.redirect ?? '');
    const safe = target.startsWith('/') && !target.startsWith('//');

    router.push(safe ? target : homeFor(user.role));
}
</script>

<template>
    <h1 class="text-xl font-semibold">Sign in</h1>
    <p class="mt-1 mb-5 text-sm text-mute">Shop owners and customers use the same door.</p>
    <LoginForm mode="user" @success="done" />
    <p class="mt-5 border-t border-dashed border-rule pt-4 text-sm">
        New here?
        <RouterLink to="/register" class="font-medium underline hover:text-brick">Create a customer account</RouterLink>
    </p>
</template>
