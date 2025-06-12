<template>
    <div class="space-y-1">
        <label class="text-sm font-medium text-gray-600">{{ label }}</label>
        <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :type="type"
            :placeholder="placeholder"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-300 focus:border-pink-300 transition"
        />
    </div>
</template>

<script setup>
defineProps({
    label: String,
    modelValue: [String, Number],
    type: {
        type: String,
        default: "text",
    },
    placeholder: {
        type: String,
        default: "",
    },
});

defineEmits(["update:modelValue"]);
</script>
