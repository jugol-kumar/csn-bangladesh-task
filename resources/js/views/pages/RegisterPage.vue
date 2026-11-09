<script setup>
import { useRouter } from 'vue-router';
import { homeFor } from '@/router';
import RegisterForm from '@/modules/auth/components/RegisterForm.vue';

const router = useRouter();

function done(user) {
    router.push(homeFor(user.role));
}
</script>

<template>
    <h1 class="text-xl font-semibold">Create an account</h1>
    <p class="mt-1 mb-5 text-sm text-mute">Customer accounts only. Shop owner accounts are created by an admin.</p>
    <RegisterForm @success="done" />
    <p class="mt-5 border-t border-dashed border-rule pt-4 text-sm">
        Already registered?
        <RouterLink to="/login" class="font-medium underline hover:text-brick">Sign in</RouterLink>
    </p>
</template>
