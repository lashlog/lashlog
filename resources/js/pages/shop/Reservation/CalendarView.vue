<!--  resources/js/pages/shop/reservation/CalendarView.vue -->
<template>
    <div class="p-6">
        <!-- カレンダーツールバー -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
                <button @click="goToPrev" class="text-sm text-primary-500">
                    &lt;
                </button>
                <button @click="goToToday" class="text-sm font-bold">
                    {{ formattedDate }}
                </button>
                <button @click="goToNext" class="text-sm text-primary-500">
                    &gt;
                </button>
            </div>

            <div class="flex items-center space-x-2">
                <button
                    :class="viewMode === 'day' ? activeClass : inactiveClass"
                    @click="changeViewMode('day')"
                >
                    日表示
                </button>
                <button
                    :class="viewMode === 'week' ? activeClass : inactiveClass"
                    @click="changeViewMode('week')"
                >
                    週表示
                </button>
            </div>
        </div>

        <!-- カレンダー本体 -->
        <CalendarGrid
            :viewMode="viewMode"
            :date="currentDate"
            :staffList="staffList"
            :reservations="reservations"
            :slotMinutes="shop.slotMinutes"
            @edit-reservation="$emit('edit-reservation', $event)"
            @create-reservation="$emit('create-reservation', $event)"
        />
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import axios from "axios";
import dayjs from "dayjs";
import CalendarGrid from "./CalendarGrid.vue";
import { useShopStore } from "@/stores/shop";
const shopStore = useShopStore();
const shop = computed(() => shopStore.shop);
const emit = defineEmits(["update:date"]);
const currentDate = ref(new Date());
const viewMode = ref("day");
const slotMinutes = ref(30); // 基本設定から取得した値など
const selectedReservation = ref(null); // モーダル用の選択中データ
const props = defineProps({
    reservations: Array,
    staffList: Array,
});
watch(currentDate, (newVal) => {
    emit("update:date", dayjs(newVal).format("YYYY-MM-DD"));
});
const reservations = computed(() => props.reservations);
const formattedDate = computed(() => {
    return currentDate.value.toLocaleDateString("ja-JP", {
        year: "numeric",
        month: "long",
        day: "numeric",
        weekday: "short",
    });
});

const goToToday = () => {
    currentDate.value = new Date();
};
const goToPrev = () => {
    const delta = viewMode.value === "week" ? -7 : -1;
    currentDate.value = dayjs(currentDate.value).add(delta, "day").toDate();
};
const goToNext = () => {
    const delta = viewMode.value === "week" ? 7 : 1;
    currentDate.value = dayjs(currentDate.value).add(delta, "day").toDate();
};

const changeViewMode = (mode) => {
    viewMode.value = mode;
};

onMounted(() => {
    console.log(reservations.value);
});

const activeClass = "px-3 py-1 bg-primary-500 text-white rounded shadow";
const inactiveClass = "px-3 py-1 bg-greige-100 text-gray-600 rounded";
</script>
