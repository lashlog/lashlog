<template>
    <div class="flex min-h-screen">
        <!-- メインコンテンツ -->
        <main class="flex-1 p-8">
            <!-- 基本設定 -->
            <div>
                <h2 class="text-2xl font-bold mb-4">🏠 基本設定</h2>
                <LabeledInput label="店舗名" v-model="form.name" />
                <LabeledInput label="電話番号" v-model="form.phone" />
                <LabeledInput label="住所" v-model="form.address" />
                <LabeledInput label="Google Map URL" v-model="form.map_url" />
                <div class="mt-6 flex justify-end">
                    <button
                        @click="submit"
                        class="btn bg-primary-500 text-white text-lg transition hover:shadow-md"
                    >
                        💾 保存する
                    </button>
                </div>
            </div>

            <!-- スタッフ設定 -->
            <div v-if="activeTab === 'staff'">
                <h2 class="text-2xl font-bold mb-4">👤 スタッフ設定</h2>
                <!-- スタッフ関連フォームがここに入ります -->
            </div>
        </main>
    </div>
</template>

<script setup>
import LabeledInput from "@/components/ui/LabeledInput.vue";
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useShopStore } from "@/stores/shop";
const shopStore = useShopStore();
const shop = computed(() => shopStore.shop);

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
            const { data } = await axios.post("api/shops", payload);
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
        const { data } = await axios.get("api/shops");
        if (data.length > 0) {
            form.value = {
                ...data[0],
                closed_days: data[0].closed_days
                    ? data[0].closed_days.split(",").map(Number)
                    : [],
                calendar_schedule: {},
            };
            if (data[0].schedules) {
                data[0].schedules.forEach((s) => {
                    if (!form.value.calendar_schedule[s.date]) {
                        form.value.calendar_schedule[s.date] = [];
                    }
                    form.value.calendar_schedule[s.date].push({
                        start: s.open_time,
                        end: s.close_time,
                    });
                });
            }
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
