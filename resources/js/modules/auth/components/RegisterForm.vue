<script setup>
import { reactive } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useFormSubmit } from '@/modules/shared/composables/useFormSubmit';
import FormField from '@/modules/shared/components/FormField.vue';

const emit = defineEmits(['success']);

const auth = useAuthStore();
const form = reactive({ name: '', email: '', password: '', password_confirmation: '' });
const { busy, run, err } = useFormSubmit();

async function submit() {
    const user = await run(() => auth.register(form));

    if (user) emit('success', user);
}
</script>

<template>
    <form class="space-y-4" @submit.prevent="submit">
        <FormField label="Name" :error="err('name')">
            <input v-model="form.name" type="text" class="input" autocomplete="name" required />
        </FormField>
        <FormField label="Email" :error="err('email')">
            <input v-model="form.email" type="email" class="input" autocomplete="email" required />
        </FormField>
        <FormField label="Password" :error="err('password')" hint="At least 8 characters.">
            <input v-model="form.password" type="password" class="input" autocomplete="new-password" required />
        </FormField>
        <FormField label="Confirm password">
            <input v-model="form.password_confirmation" type="password" class="input" autocomplete="new-password" required />
        </FormField>
        <button class="btn btn-solid w-full py-3" :disabled="busy">{{ busy ? 'Creating…' : 'Create account' }}</button>
    </form>
</template>
