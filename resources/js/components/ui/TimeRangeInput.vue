<template>
    <div class="flex items-center gap-3 text-sm p-2">
        <span class="text-gray-600 w-12">{{ label }}</span>
        <input
            :value="start"
            @input="$emit('update:start', $event.target.value)"
            type="time"
            class="px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-300 transition"
        />
        <span>〜</span>
        <input
            :value="end"
            @input="$emit('update:end', $event.target.value)"
            type="time"
            class="px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-300 transition"
        />
    </div>
</template>

<script setup>
defineProps({
    label: String,
    start: String,
    end: String,
});
defineEmits(["update:start", "update:end"]);
</script>
