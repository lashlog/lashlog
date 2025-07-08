<template>
    <div class="p-6 bg-white rounded-xl shadow-md max-w-3xl mx-auto">
        <h2 class="text-xl font-bold text-primary-600 mb-6">営業時間設定</h2>

        <form @submit.prevent="saveOpenHours" class="space-y-6">
            <div
                v-for="day in day_of_weeks"
                :key="day.value"
                class="border-b pb-4"
            >
                <div class="flex items-center justify-between">
                    <label class="text-base font-semibold text-greige-800">{{
                        day.label
                    }}</label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            class="sr-only peer"
                            v-model="openHours[day.value].enabled"
                        />
                        <div
                            class="w-11 h-6 bg-greige-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 rounded-full peer peer-checked:bg-primary-500 transition-colors"
                        ></div>
                    </label>
                </div>

                <div
                    v-if="openHours[day.value].enabled"
                    class="space-y-3 mt-4 ml-4"
                >
                    <div
                        v-for="(slot, idx) in openHours[day.value].slots"
                        :key="idx"
                        class="flex items-center gap-2 bg-greige-50 p-2 rounded-lg shadow-sm"
                    >
                        <input
                            type="time"
                            v-model="slot.open"
                            class="border border-greige-300 rounded-md px-2 py-1 w-28"
                        />
                        <span class="text-greige-600">〜</span>
                        <input
                            type="time"
                            v-model="slot.close"
                            class="border border-greige-300 rounded-md px-2 py-1 w-28"
                        />
                        <button
                            type="button"
                            @click="removeSlot(day.value, idx)"
                            class="text-red-500 text-xs hover:underline"
                        >
                            削除
                        </button>
                    </div>

                    <button
                        type="button"
                        @click="addSlot(day.value)"
                        class="text-sm text-primary-500 hover:underline mt-1"
                    >
                        ＋ 時間枠を追加
                    </button>
                </div>
            </div>

            <button
                type="submit"
                class="px-6 py-2 rounded-xl shadow-sm text-white font-medium hover:opacity-90 transition bg-primary-500"
            >
                保存する
            </button>

            <p v-if="message" class="mt-3 text-green-600 text-sm">
                {{ message }}
            </p>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const day_of_weeks = [
    { value: 0, label: "日曜日" },
    { value: 1, label: "月曜日" },
    { value: 2, label: "火曜日" },
    { value: 3, label: "水曜日" },
    { value: 4, label: "木曜日" },
    { value: 5, label: "金曜日" },
    { value: 6, label: "土曜日" },
];

const openHours = ref({});

for (let i = 0; i <= 6; i++) {
    openHours.value[i] = {
        enabled: false,
        slots: [],
    };
}

const message = ref("");

onMounted(async () => {
    const res = await axios.get("/api/shop/shop-open-hours");
    if (res.data) {
        res.data.forEach((hour) => {
            if (!openHours.value[hour.day_of_week].enabled) {
                openHours.value[hour.day_of_week].enabled = true;
            }
            openHours.value[hour.day_of_week].slots.push({
                open: hour.open_time?.slice(0, 5) || "",
                close: hour.close_time?.slice(0, 5) || "",
            });
        });
    }
});

const addSlot = (day_of_week) => {
    openHours.value[day_of_week].slots.push({ open: "", close: "" });
};

const removeSlot = (day_of_week, index) => {
    openHours.value[day_of_week].slots.splice(index, 1);
};

const saveOpenHours = async () => {
    const payload = [];
    Object.entries(openHours.value).forEach(([day_of_week, info]) => {
        if (info.enabled) {
            info.slots.forEach((slot) => {
                payload.push({
                    day_of_week: parseInt(day_of_week),
                    open_time: slot.open,
                    close_time: slot.close,
                });
            });
        }
    });

    await axios.post("/api/shop/shop-open-hours", { hours: payload });
    message.value = "保存しました";
};
</script>

<style scoped></style>
