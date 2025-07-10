<template>
    <div class="flex min-h-screen">
        <main class="flex-1 p-8">
            <div>
                <h2 class="text-2xl font-bold mb-4">📅 営業日設定</h2>

                <div class="flex border-b border-gray-300 mb-4">
                    <button
                        :class="[
                            'px-4 py-2 border-b-2 font-semibold',
                            form.schedule_type === 'weekday'
                                ? 'border-primary-500 text-primary-500'
                                : 'border-transparent text-gray-500 hover:text-primary-500 hover:cursor-pointer',
                        ]"
                        @click="form.schedule_type = 'weekday'"
                    >
                        📅 曜日ごとで設定
                    </button>

                    <button
                        :class="[
                            'px-4 py-2 border-b-2 font-semibold',
                            form.schedule_type === 'calendar'
                                ? 'border-primary-500 text-primary-500'
                                : 'border-transparent text-gray-500 hover:text-primary-500 hover:cursor-pointer',
                        ]"
                        @click="form.schedule_type = 'calendar'"
                    >
                        📆 カレンダーで設定
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
        </main>
    </div>
</template>
<script setup>
import { ref, computed } from "vue";

import LabeledInput from "@/components/ui/LabeledInput.vue";
import CalendarSchedule from "@/pages/shop/settings/schedule/CalendarSchedule.vue";
import WeekdaySchedule from "@/pages/shop/settings/schedule/WeekdaySchedule.vue";
import axios from "axios";
import { useShopStore } from "@/stores/shop";
const shopStore = useShopStore();
const shop = computed(() => shopStore.shop);
const form = ref({
    schedule_type: "calendar",
    calendar_schedule: [],
    weekday_open_time: "",
    weekday_close_time: "",
    weekend_open_time: "",
    weekend_close_time: "",
    closed_days: [],
});
</script>
