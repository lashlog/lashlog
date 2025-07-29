<!-- resources/js/pages/shop/reservation/ReservationPage.vue -->
<template>
    <div>
        <CalendarView
            :staffList="staffList"
            :reservations="reservations"
            @edit-reservation="openModal"
            @create-reservation="openNewModal"
            @update:date="handleDateChange"
        />

        <EditReservationModal
            v-if="selectedReservation"
            :reservation="selectedReservation"
            :reservation-list="reservations"
            @close="selectedReservation = null"
            @saved="reloadReservations"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import CalendarView from "./CalendarView.vue";
import EditReservationModal from "./EditReservationModal.vue";
import { useShopStore } from "@/stores/shop";
import dayjs from "dayjs";
import axios from "axios";

const staffList = ref([]); // ← APIで取得
const reservations = ref([]); // ← APIで取得
const businessHours = ref([]); // ← 営業時間情報

const selectedReservation = ref(null);
const shopStore = useShopStore();
const shop = computed(() => shopStore.shop);
const currentDate = ref(dayjs().format("YYYY-MM-DD"));
const fetchBusinessHours = async (dateStr) => {
    const res = await axios.get("/api/shop/business-hours", {
        params: { date: dateStr },
    });
    businessHours.value = res.data;
};
const openModal = (res) => {
    selectedReservation.value = res;
};
// 新規予約（空スロット）を選択したとき
const openNewModal = (info) => {
    console.log("新規予約モーダルを開く", info);
    console.log("add", shop.value.slot_minutes);
    console.log(
        "end_time",
        dayjs(`${info.reserved_date} ${info.start_time}`).format("HH:mm")
    );
    selectedReservation.value = {
        id: null, // 新規
        reserved_date: info.reserved_date,
        start_time: info.start_time,
        end_time: dayjs(`${info.reserved_date} ${info.start_time}`)
            .add(shop.value.slot_minutes, "minute")
            .format("HH:mm"),
        staff_id: info.staff_id,
        customer: null,
    };
    console.log("新規予約モーダルを開く", selectedReservation.value);
};
const reloadReservations = async () => {
    if (!shop.value) return;
    try {
        const res = await axios.get("/api/shop/reservations", {
            params: {
                date: currentDate.value, // ← 今日の日付などに変更してもOK
            },
        });
        reservations.value = res.data;
    } catch (e) {
        console.error("予約取得失敗", e);
    }
};
// スタッフ取得
const fetchStaff = async () => {
    try {
        const res = await axios.get("/api/shop/staffs", {
            withCredentials: true,
        });
        staffList.value = res.data;
    } catch (error) {
        console.error("スタッフ取得エラー", error);
    }
};
const handleDateChange = async (date) => {
    currentDate.value = date;
    await reloadReservations();
};
onMounted(() => {
    reloadReservations();
    fetchStaff();
    fetchBusinessHours(currentDate.value);
});
</script>
