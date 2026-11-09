<script setup>
defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Search' },
});
const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <input
        type="search"
        class="input"
        :value="modelValue"
        :placeholder="placeholder"
        @input="emit('update:modelValue', $event.target.value)"
    />
</template>
