<!--  resources/js/pages/shop/reservation/CalendarGrid.vue -->
<template>
    <div class="relative overflow-x-auto border rounded-xl bg-white shadow">
        <!-- 時間帯の目盛り列 -->
        <div class="absolute left-0 top-0 z-10 w-20 border-r bg-gray-50 h-full">
            <div
                class="sticky top-0 z-10 bg-white text-center font-semibold py-1 border-b"
                style="height: 33px"
            >
                時間
            </div>
            <div
                v-for="slot in displayedSlots"
                :key="slot"
                :style="{ height: `${slotHeight}px` }"
                class="px-1 text-xs text-right border-b border-gray-200"
            >
                {{ slot }}
            </div>
        </div>

        <!-- スタッフごとの予約列 -->
        <div class="ml-20 flex">
            <div
                v-for="staff in staffList"
                :key="staff.id"
                class="relative flex-1 border-l border-gray-200"
                :class="{ 'bg-gray-200': isClosed(today) }"
            >
                <!-- スタッフ名 -->
                <div
                    class="sticky top-0 z-10 bg-white text-center font-semibold py-1 border-b"
                    style="height: 33px"
                >
                    {{ staff.name }}
                </div>

                <!-- 各時間スロット -->
                <div
                    v-for="slot in displayedSlots"
                    :key="slot"
                    :style="{ height: `${slotHeight}px` }"
                    class="border-b border-gray-100"
                    :class="[
                        isSlotAvailable(slot)
                            ? 'bg-transparent hover:bg-greige-50'
                            : 'bg-gray-200 pointer-events-none', // ← 営業外スロットはグレージュで固定
                    ]"
                    @dblclick="onSlotDblClick(slot, staff.id)"
                    @click="console.log('click', slot, staff.id)"
                ></div>

                <!-- 予約ブロック -->
                <div
                    v-for="res in reservationsByStaff(staff.id)"
                    :key="res.id"
                    class="absolute left-1 right-1 bg-primary-500 text-white text-xs px-2 py-1 rounded shadow cursor-pointer"
                    :style="getReservationStyle(res)"
                    @dblclick="$emit('edit-reservation', res)"
                >
                    {{ res.customer?.name || "無名" }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, watch } from "vue";
import dayjs from "dayjs";
import "dayjs/locale/ja";
import isSameOrAfter from "dayjs/plugin/isSameOrAfter";

// import isBefore from "dayjs/plugin/isBefore";

dayjs.extend(isSameOrAfter);
// dayjs.extend(isBefore);
const emit = defineEmits([
    "edit-reservation",
    "create-reservation",
    "update:slots",
]);

// props
const props = defineProps({
    staffList: Array,
    reservations: Array,
    currentDate: String, // 追加: 現在表示中の日付
    openTime: { type: String, default: "09:00" },
    closeTime: { type: String, default: "18:00" },
    slotMinutes: { type: Number, default: 30 }, // 🆕 予約単位時間
    slotHeight: { type: Number, default: 50 }, // px
    businessHours: { type: Object, default: () => ({}) }, // 🆕 営業時間情報
});
console.log("props", props);
// const emit = defineEmits(["edit-reservation"]);
const today = dayjs().format("YYYY-MM-DD");

const isClosed = () => {
    if (!Array.isArray(props.businessHours)) return true;
    return props.businessHours.every((item) => item.is_closed === true);
};
// 表示スロット（例: ["09:00", "09:30", "10:00", ...]）
const displayedSlots = computed(() => {
    console.log("displayedSlots", props.businessHours);
    const hours = Array.isArray(props.businessHours)
        ? props.businessHours
        : [props.businessHours];

    const openTimes = hours.map((h) => h.open_time).filter(Boolean);
    const closeTimes = hours.map((h) => h.close_time).filter(Boolean);

    // fallback: props.openTime / closeTime
    const minTime = openTimes.length
        ? openTimes.reduce((a, b) => (a < b ? a : b))
        : props.openTime;

    const maxTime = closeTimes.length
        ? closeTimes.reduce((a, b) => (a > b ? a : b))
        : props.closeTime;

    const [startH, startM] = minTime.split(":").map(Number);
    const [endH, endM] = maxTime.split(":").map(Number);

    const startTotal = startH * 60 + startM;
    const endTotal = endH * 60 + endM;

    const slots = [];
    for (let min = startTotal; min < endTotal; min += props.slotMinutes) {
        const h = Math.floor(min / 60);
        const m = min % 60;
        slots.push(
            `${String(h).padStart(2, "0")}:${String(m).padStart(2, "0")}`
        );
    }

    return slots;
});
watch(displayedSlots, (newVal) => {
    emit("update:slots", newVal); // 親に渡す
});
// スタッフごとにフィルタ
const reservationsByStaff = (staffId) => {
    return props.reservations.filter((r) => r.staff_id === staffId);
};

// 予約ブロックの位置・サイズ計算（1 slot = 24px）
const getReservationStyle = (res) => {
    const start = dayjs(`2000-01-01T${res.start_time}`);
    const end = dayjs(`2000-01-01T${res.end_time}`);
    const open = dayjs(`2000-01-01T${props.openTime}`);

    const topOffset = 33; // スタッフ名ヘッダーの高さ（px）

    const top =
        start.diff(open, "minute") * (props.slotHeight / props.slotMinutes) +
        topOffset;
    const height =
        end.diff(start, "minute") * (props.slotHeight / props.slotMinutes) - 1;

    return {
        top: `${top}px`,
        height: `${height}px`,
    };
};

const onSlotDblClick = (slot, staffId) => {
    const reservedDate = dayjs().format("YYYY-MM-DD"); // または props で渡された現在表示中の日付
    emit("create-reservation", {
        reserved_date: reservedDate, // 追加！！
        start_time: slot,
        staff_id: staffId,
    });
};
const isSlotAvailable = (slotTimeStr) => {
    const hours = Array.isArray(props.businessHours)
        ? props.businessHours
        : [props.businessHours];

    // もしすべてが is_closed ならその日は完全に休み
    const isAllClosed = hours.every((h) => h.is_closed);
    if (isAllClosed) return false;

    // どれか1つでも slot に該当していれば OK
    return hours.some((h) => {
        if (h.is_closed) return false;
        const slot = dayjs(`2000-01-01T${slotTimeStr}`);
        const start = dayjs(`2000-01-01T${h.open_time}`);
        const end = dayjs(`2000-01-01T${h.close_time}`);
        return slot.isSameOrAfter(start) && slot.isBefore(end);
    });
};
</script>

<style scoped>
/* TailwindCSS ベースで特別なCSSは不要 */
</style>
