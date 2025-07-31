<template>
    <button
        :class="buttonClass"
        :disabled="disabled || loading"
        v-bind="$attrs"
    >
        <span
            v-if="loading"
            class="animate-spin mr-2 h-4 w-4 border-2 border-t-transparent rounded-full border-white"
        ></span>
        <slot />
    </button>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    variant: {
        type: String,
        default: "primary", // 'primary', 'secondary', 'outline', etc.
    },
    size: {
        type: String,
        default: "md", // 'sm', 'md', 'lg'
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const baseClasses =
    "inline-flex items-center justify-center font-medium rounded-2xl focus:outline-none transition-all duration-200";

const variants = {
    primary:
        "bg-primary-500 text-white hover:bg-primary-600 disabled:bg-primary-300",
    secondary:
        "bg-greige-500 text-white hover:bg-greige-600 disabled:bg-greige-300",
    outline:
        "border border-primary-500 text-primary-500 hover:bg-primary-50 disabled:opacity-50",
};

const sizes = {
    sm: "px-3 py-1.5 text-sm",
    md: "px-4 py-2 text-base",
    lg: "px-6 py-3 text-lg",
};

const buttonClass = computed(() => {
    return `${baseClasses} ${variants[props.variant]} ${sizes[props.size]}`;
});
</script>
