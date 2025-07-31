<template>
    <div class="space-y-1 w-full">
        <label
            v-if="label"
            :for="id"
            class="block text-sm font-medium text-gray-700"
        >
            {{ label }}
        </label>
        <textarea
            :id="id"
            v-model="modelValue"
            :rows="rows"
            :placeholder="placeholder"
            :class="[
                'block w-full rounded-2xl border px-4 py-2 text-sm shadow-sm focus:outline-none focus:ring-2',
                error
                    ? 'border-red-400 focus:ring-red-200'
                    : 'border-gray-300 focus:ring-primary-100',
                disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white',
            ]"
            :disabled="disabled"
        ></textarea>
        <p v-if="error" class="text-xs text-red-500 mt-1">{{ error }}</p>
    </div>
</template>

<script setup>
import { computed, useAttrs } from "vue";

const props = defineProps({
    label: String,
    modelValue: String,
    placeholder: String,
    error: String,
    rows: {
        type: Number,
        default: 4,
    },
    id: {
        type: String,
        default: () => `textarea-${Math.random().toString(36).substring(2, 9)}`,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

const modelValue = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});
</script>
