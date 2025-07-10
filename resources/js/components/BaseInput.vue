<!-- components/BaseInput.vue -->
<template>
    <div>
        <label class="block mb-1 font-semibold">{{ label }}</label>
        <input
            v-bind="$attrs"
            :type="type"
            class="w-full border border-gray-300 rounded px-4 py-2"
        />
    </div>
</template>

<script setup>
defineProps({
    label: String,
    type: { type: String, default: "text" },
});
</script>
