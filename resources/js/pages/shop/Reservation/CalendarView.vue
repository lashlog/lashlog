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
            <button
                :class="viewMode === 'month' ? activeClass : inactiveClass"
                @click="changeViewMode('month')"
            >
                月表示
            </button>
        </div>
        </div>

        <!-- カレンダー本体 -->
        <MonthView
            v-if="viewMode === 'month'"
            :reservations-by-date="reservationsByDate"
            :current-month="currentMonth"
            @select-date="emit('update:date', $event)"
            @update:currentMonth="handleMonthChange"
        />
        <CalendarGrid
            v-else
            v-model:slots="slots"
            :viewMode="viewMode"
            :date="props.currentDate"
            :staffList="staffList"
            :reservations="reservations"
            :slotMinutes="shop.slotMinutes"
            :businessHours="businessHours"
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
import MonthView from "./MonthView.vue";
import { useShopStore } from "@/stores/shop";
const shopStore = useShopStore();
const shop = computed(() => shopStore.shop);
const viewMode = ref("day");
const slotMinutes = ref(30); // 基本設定から取得した値など
const selectedReservation = ref(null); // モーダル用の選択中データ
const slots = ref([]);
const emit = defineEmits(["update:date", "update:slots"]);
watch(slots, (newVal) => {
    emit("update:slots", newVal);
});
const props = defineProps({
    reservations: Array,
    staffList: Array,
    businessHours: Array, // 営業時間情報
    currentDate: String,
});
const currentMonth = ref(dayjs(props.currentDate).format('YYYY-MM'));
watch(
    () => props.currentDate,
    (newVal) => {
        currentMonth.value = dayjs(newVal).format('YYYY-MM');
        emit('update:date', dayjs(newVal).format('YYYY-MM-DD'));
    }
);

const reservations = computed(() => props.reservations);
const reservationsByDate = computed(() => {
    const map = {};
    for (const r of props.reservations) {
        const date = r.reserved_date;
        if (!map[date]) map[date] = [];
        map[date].push({
            id: r.id,
            customer_name: r.customer?.name || r.customer_name || '',
            menu_name: r.menu?.name || r.menu_name || '',
        });
    }
    return Object.keys(map).map((d) => ({ date: d, reservations: map[d] }));
});
const formattedDate = computed(() => {
    if (viewMode.value === 'month') {
        return dayjs(currentMonth.value + '-01').format('YYYY年MM月');
    }
    return dayjs(props.currentDate).format('YYYY年MM月DD日(dd)');
});

const goToToday = () => {
    emit('update:date', dayjs().format('YYYY-MM-DD'));
};
const goToPrev = () => {
    let unit = "day";
    if (viewMode.value === "week") unit = "week";
    if (viewMode.value === "month") unit = "month";
    const newDate = dayjs(props.currentDate).subtract(1, unit).format('YYYY-MM-DD');
    emit('update:date', newDate);
};
const goToNext = () => {
    let unit = "day";
    if (viewMode.value === "week") unit = "week";
    if (viewMode.value === "month") unit = "month";
    const newDate = dayjs(props.currentDate).add(1, unit).format('YYYY-MM-DD');
    emit('update:date', newDate);
};

const changeViewMode = (mode) => {
    viewMode.value = mode;
};

const handleMonthChange = (newMonth) => {
    currentMonth.value = newMonth;
    emit('update:date', dayjs(newMonth + '-01').format('YYYY-MM-DD'));
};

onMounted(() => {
    console.log(reservations.value);
});

const activeClass = "px-3 py-1 bg-primary-500 text-white rounded shadow";
const inactiveClass = "px-3 py-1 bg-greige-100 text-gray-600 rounded";
</script>

