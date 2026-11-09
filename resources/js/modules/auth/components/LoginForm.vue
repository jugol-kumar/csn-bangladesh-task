<script setup>
import { reactive } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useFormSubmit } from '@/modules/shared/composables/useFormSubmit';
import FormField from '@/modules/shared/components/FormField.vue';

const props = defineProps({ mode: { type: String, default: 'user' } });
const emit = defineEmits(['success']);

const auth = useAuthStore();
const form = reactive({ email: '', password: '' });
const { busy, run, err } = useFormSubmit();

async function submit() {
    const user = await run(() => (props.mode === 'admin' ? auth.adminLogin(form) : auth.login(form)));

    if (user) emit('success', user);
}
</script>

<template>
    <form class="space-y-4" @submit.prevent="submit">
        <FormField label="Email" :error="err('email')">
            <input v-model="form.email" type="email" class="input" autocomplete="username" required />
        </FormField>
        <FormField label="Password" :error="err('password')">
            <input v-model="form.password" type="password" class="input" autocomplete="current-password" required />
        </FormField>
        <button class="btn btn-solid w-full py-3" :disabled="busy">{{ busy ? 'Signing in…' : 'Sign in' }}</button>
    </form>
</template>
