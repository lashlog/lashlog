// resources/js/components/ui/dialog/Dialog.vue
<template>
    <transition name="fade">
        <div
            v-if="modelValue"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        >
            <div
                class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 relative"
            >
                <slot />
                <button
                    @click="$emit('update:modelValue', false)"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
                >
                    &#10005;
                </button>
            </div>
        </div>
    </transition>
</template>

<script setup>
defineProps({
    modelValue: Boolean,
});

defineEmits(["update:modelValue"]);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
