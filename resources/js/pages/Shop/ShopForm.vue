<!-- resources/js/pages/Shop/ShopForm.vue -->
<template>
    <div class="p-8 mx-auto mt-12">
        <h2 class="text-3xl font-bold mb-6 flex items-center gap-2">
            🏠 店舗設定
        </h2>

        <!-- 入力フォーム -->
        <div class="space-y-6 mt-6 max-w-2xl">
            <LabeledInput label="店舗名" v-model="form.name" />
            <LabeledInput label="電話番号" v-model="form.phone" />
            <LabeledInput label="住所" v-model="form.address" />
            <LabeledInput label="Google Map URL" v-model="form.map_url" />
        </div>

        <!-- 営業時間 -->
        <div class="mt-10 max-w-2xl">
            <h3 class="text-lg font-bold text-gray-700 mb-3">営業時間</h3>
            <TimeRangeInput
                label="平日："
                :start="form.weekday_open_time"
                :end="form.weekday_close_time"
                @update:start="form.weekday_open_time = $event"
                @update:end="form.weekday_close_time = $event"
            />
            <TimeRangeInput
                label="土日："
                :start="form.weekend_open_time"
                :end="form.weekend_close_time"
                @update:start="form.weekend_open_time = $event"
                @update:end="form.weekend_close_time = $event"
            />
        </div>

        <!-- 定休日 -->
        <div class="my-10">
            <h3 class="text-lg font-bold text-gray-700 mb-3">定休日</h3>
            <div class="flex flex-wrap gap-4">
                <label
                    v-for="(day, index) in days"
                    :key="index"
                    class="flex items-center gap-2 text-sm text-gray-700"
                >
                    <input
                        type="checkbox"
                        :value="index + 1"
                        v-model="form.closed_days"
                        class="accent-primary-300 w-4 h-4"
                    />
                    {{ day }}
                </label>
            </div>
        </div>

        <button
            @click="submit"
            class="pt-5 btn bg-primary-500 text-white mt-10 text-lg transition hover:shadow-md"
        >
            💾 保存する
        </button>
    </div>
</template>

<script setup>
import LabeledInput from "@/components/ui/LabeledInput.vue";
import TimeRangeInput from "@/components/ui/TimeRangeInput.vue";
import { ref, onMounted } from "vue";
import axios from "axios";

const days = ["月", "火", "水", "木", "金", "土", "日"];

const form = ref({
    name: "",
    phone: "",
    address: "",
    map_url: "",
    weekday_open_time: "",
    weekday_close_time: "",
    weekend_open_time: "",
    weekend_close_time: "",
    closed_days: [],
});

const submit = async () => {
    try {
        const payload = {
            ...form.value,
            closed_days: form.value.closed_days.join(","),
        };

        if (form.value.id) {
            // 更新（PUT）
            await axios.put(`/shops/${form.value.id}`, payload);
        } else {
            // 新規登録（POST）
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
    const { data } = await axios.get("/shops");
    if (data.length > 0) {
        form.value = {
            ...data[0],
            closed_days: data[0].closed_days
                ? data[0].closed_days.split(",").map(Number)
                : [],
        };
    }
});
</script>
