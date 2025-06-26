<template>
    <div class="space-y-2">
        <label
            v-for="option in options"
            :key="option.value"
            class="flex items-center gap-2"
        >
            <input
                type="radio"
                :value="option.value"
                :name="name"
                :checked="modelValue === option.value"
                class="accent-primary"
                @change="updateValue(option.value)"
            />
            <span class="text-sm text-gray-800">{{ option.label }}</span>
        </label>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: [String, Number],
    name: String,
    options: Array,
});

const emit = defineEmits(["update:modelValue"]);

const updateValue = (val) => {
    emit("update:modelValue", val);
};
</script>
