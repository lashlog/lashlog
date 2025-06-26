<template>
    <div class="flex min-h-screen">
        <!-- サイドバー -->
        <aside class="w-64 bg-gray-100 border-r p-6">
            <h2 class="text-xl font-bold mb-6">設定メニュー</h2>
            <ul class="space-y-4">
                <li>
                    <button
                        class="w-full text-left"
                        :class="{
                            'font-bold text-blue-600': activeTab === 'basic',
                        }"
                        @click="activeTab = 'basic'"
                    >
                        基本設定
                    </button>
                </li>
                <li>
                    <button
                        class="w-full text-left"
                        :class="{
                            'font-bold text-blue-600': activeTab === 'schedule',
                        }"
                        @click="activeTab = 'schedule'"
                    >
                        営業日設定
                    </button>
                </li>
                <li>
                    <button
                        class="w-full text-left"
                        :class="{
                            'font-bold text-blue-600': activeTab === 'staff',
                        }"
                        @click="activeTab = 'staff'"
                    >
                        スタッフ設定
                    </button>
                </li>
            </ul>
        </aside>

        <!-- メインコンテンツ -->
        <main class="flex-1 p-8">
            <!-- 基本設定 -->
            <div v-if="activeTab === 'basic'">
                <h2 class="text-2xl font-bold mb-4">🏠 基本設定</h2>
                <LabeledInput label="店舗名" v-model="form.name" />
                <LabeledInput label="電話番号" v-model="form.phone" />
                <LabeledInput label="住所" v-model="form.address" />
                <LabeledInput label="Google Map URL" v-model="form.map_url" />
            </div>

            <!-- 営業日設定 -->
            <div v-if="activeTab === 'schedule'">
                <h2 class="text-2xl font-bold mb-4">📅 営業日設定</h2>

                <div class="flex border-b border-gray-300 mb-4">
                    <button
                        :class="[
                            'px-4 py-2 border-b-2 font-semibold',
                            form.schedule_type === 'calendar'
                                ? 'border-blue-500 text-blue-500'
                                : 'border-transparent text-gray-500 hover:text-blue-500',
                        ]"
                        @click="form.schedule_type = 'calendar'"
                    >
                        📆 カレンダーで設定
                    </button>
                    <button
                        :class="[
                            'px-4 py-2 border-b-2 font-semibold',
                            form.schedule_type === 'weekday'
                                ? 'border-blue-500 text-blue-500'
                                : 'border-transparent text-gray-500 hover:text-blue-500',
                        ]"
                        @click="form.schedule_type = 'weekday'"
                    >
                        📅 曜日ごとで設定
                    </button>
                </div>

                <CalendarSchedule
                    v-if="form.schedule_type === 'calendar'"
                    v-model="form.calendar_schedule"
                />
                <WeekdaySchedule
                    v-if="form.schedule_type === 'weekday'"
                    v-model:weekday_open_time="form.weekday_open_time"
                    v-model:weekday_close_time="form.weekday_close_time"
                    v-model:weekend_open_time="form.weekend_open_time"
                    v-model:weekend_close_time="form.weekend_close_time"
                    v-model:closed_days="form.closed_days"
                />
            </div>

            <!-- スタッフ設定 -->
            <div v-if="activeTab === 'staff'">
                <h2 class="text-2xl font-bold mb-4">👤 スタッフ設定</h2>
                <!-- スタッフ関連フォームがここに入ります -->
            </div>

            <button
                @click="submit"
                class="pt-5 btn bg-primary-500 text-white mt-10 text-lg transition hover:shadow-md"
            >
                💾 保存する
            </button>
        </main>
    </div>
</template>

<script setup>
import LabeledInput from "@/components/ui/LabeledInput.vue";
import CalendarSchedule from "@/pages/admin/Shop/Schedule/CalendarSchedule.vue";
import WeekdaySchedule from "@/pages/admin/Shop/Schedule/WeekdaySchedule.vue";
import { ref, onMounted } from "vue";
import axios from "axios";

const activeTab = ref("basic");

const form = ref({
    name: "",
    phone: "",
    address: "",
    map_url: "",
    schedule_type: "calendar",
    weekday_open_time: "",
    weekday_close_time: "",
    weekend_open_time: "",
    weekend_close_time: "",
    closed_days: [],
    calendar_schedule: [],
});

const submit = async () => {
    try {
        const payload = {
            ...form.value,
            closed_days: form.value.closed_days.join(","),
            calendar_schedule: JSON.stringify(form.value.calendar_schedule),
        };
        if (form.value.id) {
            await axios.put(`/shops/${form.value.id}`, payload);
        } else {
            const { data } = await axios.post("/shops", payload);
            form.value.id = data.id;
        }
        alert("保存しました");
    } catch (e) {
        alert("保存に失敗しました");
        console.error(e);
    }
};

onMounted(async () => {
    try {
        const { data } = await axios.get("/shops");
        if (data.length > 0) {
            form.value = {
                ...data[0],
                closed_days: data[0].closed_days
                    ? data[0].closed_days.split(",").map(Number)
                    : [],
                calendar_schedule: data[0].calendar_schedule
                    ? JSON.parse(data[0].calendar_schedule)
                    : [],
            };
        }
    } catch (e) {
        console.error("店舗情報の取得に失敗しました", e);
    }
});
</script>

<style scoped>
.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
}
</style>
