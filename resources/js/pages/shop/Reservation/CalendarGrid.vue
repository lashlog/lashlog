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
                    class="border-b border-gray-100 bg-transparent hover:bg-greige-50 cursor-pointer"
                    @dblclick="isOpenDay && onSlotDblClick(slot, staff.id)"
                    @click="console.log('click', slot, staff.id)"
                ></div>

                <!-- 予約ブロック -->
                <div
                    v-for="res in reservationsByStaff(staff.id)"
                    :key="res.id"
                    class="absolute left-1 right-1 bg-primary-500 text-white text-xs px-2 py-1 rounded shadow cursor-pointer"
                    :style="getReservationStyle(res)"
                    @dblclick="isOpenDay && $emit('edit-reservation', res)"
                >
                    {{ res.customer?.name || "無名" }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import dayjs from "dayjs";
import "dayjs/locale/ja";

// props
const props = defineProps({
    staffList: Array,
    reservations: Array,
    openTime: { type: String, default: "09:00" },
    closeTime: { type: String, default: "18:00" },
    slotMinutes: { type: Number, default: 30 }, // 🆕 予約単位時間
    slotHeight: { type: Number, default: 50 }, // px
    isOpenDay: { type: Boolean, default: true }, // 🆕 予約可能日かどうか
});

// const emit = defineEmits(["edit-reservation"]);

// 表示スロット（例: ["09:00", "09:30", "10:00", ...]）
const displayedSlots = computed(() => {
    const [startH, startM] = props.openTime.split(":").map(Number);
    const [endH, endM] = props.closeTime.split(":").map(Number);
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
const emit = defineEmits(["edit-reservation", "create-reservation"]);

const onSlotDblClick = (slot, staffId) => {
    const reservedDate = dayjs().format("YYYY-MM-DD"); // または props で渡された現在表示中の日付
    emit("create-reservation", {
        reserved_date: reservedDate, // 追加！！
        start_time: slot,
        staff_id: staffId,
    });
};
</script>

<style scoped>
/* TailwindCSS ベースで特別なCSSは不要 */
</style>
