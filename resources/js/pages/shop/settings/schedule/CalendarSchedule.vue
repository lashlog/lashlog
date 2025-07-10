<template>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow">
        <div class="flex items-center justify-between mb-4">
            <button
                @click="prevMonth"
                class="text-primary-600 hover:cursor-pointer"
            >
                ← 前の月
            </button>
            <h2 class="text-xl font-bold">
                {{ currentYear }}年 {{ currentMonth + 1 }}月 の営業スケジュール
            </h2>
            <button
                @click="nextMonth"
                class="text-primary-600 hover:cursor-pointer"
            >
                次の月 →
            </button>
        </div>

        <div class="grid grid-cols-7 gap-2">
            <div
                v-for="day in daysInMonth"
                :key="day"
                @click="day && !isPastDay(day) && openModal(day)"
                class="p-2 border min-h-24 rounded"
                :class="[
                    day === null
                        ? 'bg-gray-200 cursor-default'
                        : isPastDay(day)
                        ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                        : 'hover:bg-gray-100 cursor-pointer',
                ]"
            >
                <div class="font-bold">{{ day }}</div>

                <div
                    v-if="schedule[formatDate(day)]?.schedule?.length"
                    class="mt-1 space-y-1"
                >
                    <div
                        v-for="(slot, idx) in schedule[formatDate(day)]
                            ?.schedule"
                        :key="idx"
                        class="text-xs text-white bg-primary-300 px-2 py-1 rounded-md shadow-sm text-center whitespace-nowrap min-w-[80px] mx-auto"
                    >
                        {{ slot.start.slice(0, 5) }} -
                        {{ slot.end.slice(0, 5) }}
                    </div>
                </div>
                <div
                    v-if="schedule[formatDate(day)]?.closed"
                    class="mt-1 text-center"
                >
                    <span
                        class="inline-block text-xl text-red-500 bg-red-100 rounded-full px-2 py-0.5"
                    >
                        休
                    </span>
                </div>
            </div>
        </div>

        <!-- モーダル -->
        <div
            v-if="isModalOpen"
            class="fixed inset-0 flex items-center justify-center bg-black/20 backdrop-opacity-50 z-50"
        >
            <div class="bg-white rounded-lg p-6 w-96">
                <h3 class="text-lg font-bold mb-4">
                    {{ selectedDate }}のスケジュール
                </h3>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input
                            type="checkbox"
                            v-model="selectedIsClosed"
                            class="mr-2"
                        />
                        この日は休みにする
                    </label>
                </div>
                <div v-if="!selectedIsClosed">
                    <div
                        v-for="(item, index) in selectedSchedule"
                        :key="index"
                        class="flex items-center gap-2 mb-2"
                    >
                        <input
                            type="time"
                            v-model="item.start"
                            class="px-2 py-1 border rounded w-full"
                        />
                        <input
                            type="time"
                            v-model="item.end"
                            class="px-2 py-1 border rounded w-full"
                        />
                        <button
                            @click="removeTime(index)"
                            class="text-red-500 text-sm"
                        >
                            削除
                        </button>
                    </div>

                    <button @click="addTime" class="text-blue-600 text-sm mb-4">
                        ＋時間帯を追加
                    </button>
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        @click="closeModal"
                        class="px-4 py-2 border rounded"
                    >
                        キャンセル
                    </button>
                    <button
                        @click="saveSchedule"
                        class="px-4 py-2 bg-blue-500 text-white rounded"
                    >
                        保存
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from "vue";
import axios from "axios";

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({}),
    },
});
const selectedIsClosed = ref(false);
onMounted(async () => {
    try {
        const res = await axios.get("/api/shop/shop-schedules");
        console.log("スケジュール取得成功", res.data);
        if (res.data && typeof res.data === "object") {
            schedule.value = res.data;
        } else {
            schedule.value = {}; // fallback
            console.warn("スケジュール形式が不正のため、空に設定しました。");
        }
    } catch (error) {
        console.error("スケジュールの取得に失敗しました", error);
        schedule.value = {}; // fallback
    }
});
const emit = defineEmits(["update:modelValue"]);

const schedule = ref({});
watch(
    () => props.modelValue,
    (val) => {
        schedule.value = { ...val };
    },
    { immediate: true, deep: true }
);

// ⬇ 月切り替え対応
const current = ref(new Date());
const currentYear = computed(() => current.value.getFullYear());
const currentMonth = computed(() => current.value.getMonth());

const daysInMonth = computed(() => {
    const days = new Date(
        currentYear.value,
        currentMonth.value + 1,
        0
    ).getDate();
    const firstDay = new Date(
        currentYear.value,
        currentMonth.value,
        1
    ).getDay(); // 0=日曜

    // 空白（前月の日にちではなく空欄）
    const blanks = Array.from({ length: firstDay }, () => null);

    // 実際の日付（1～月末）
    const dates = Array.from({ length: days }, (_, i) => i + 1);

    return [...blanks, ...dates];
});

const formatDate = (day) => {
    const mm = String(currentMonth.value + 1).padStart(2, "0");
    const dd = String(day).padStart(2, "0");
    return `${currentYear.value}-${mm}-${dd}`;
};

const prevMonth = () => {
    current.value = new Date(currentYear.value, currentMonth.value - 1, 1);
};

const nextMonth = () => {
    current.value = new Date(currentYear.value, currentMonth.value + 1, 1);
};

// モーダル関係
const isModalOpen = ref(false);
const selectedDay = ref(null);
const selectedDate = computed(() =>
    selectedDay.value ? formatDate(selectedDay.value) : ""
);
const selectedSchedule = ref([]);

const openModal = (day) => {
    const date = formatDate(day);
    const info = schedule.value[date] || [];
    selectedDay.value = day;
    selectedSchedule.value = Array.isArray(info)
        ? [...info]
        : Array.isArray(info.schedule)
        ? [...info.schedule]
        : [];

    selectedIsClosed.value = !!info.closed;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    selectedDay.value = null;
    selectedSchedule.value = [];
};

const addTime = () => {
    selectedSchedule.value.push({ start: "", end: "" });
};

const removeTime = (index) => {
    selectedSchedule.value.splice(index, 1);
};

const saveSchedule = async () => {
    const date = formatDate(selectedDay.value);
    const data = selectedSchedule.value.filter((s) => s.start && s.end);

    // ✅ 重複チェック（休みならスキップ）
    if (!selectedIsClosed.value && hasOverlap(data)) {
        alert("時間帯が重複しています。重ならないように修正してください。");
        return;
    }
    // ✅ ローカルデータ更新
    schedule.value[date] = selectedIsClosed.value
        ? { closed: true }
        : { schedule: data };
    emit("update:modelValue", { ...schedule.value });

    try {
        await axios.post("/api/shop/shop-schedules", {
            date: date,
            schedule: data,
            closed: selectedIsClosed.value,
        });
    } catch (error) {
        console.error("保存に失敗しました", error);
    }

    closeModal();
};
const isPastDay = (day) => {
    const today = new Date();
    today.setHours(0, 0, 0, 0); // 時間をリセット（正確な日付比較のため）

    const date = new Date(currentYear.value, currentMonth.value, day);
    return date < today;
};
function hasOverlap(times) {
    const sorted = times
        .map((t) => ({
            start: t.start,
            end: t.end,
        }))
        .sort((a, b) => a.start.localeCompare(b.start));

    for (let i = 0; i < sorted.length - 1; i++) {
        const curr = sorted[i];
        const next = sorted[i + 1];

        if (curr.end > next.start) {
            return true; // 重複あり
        }
    }
    return false;
}
</script>

<style scoped></style>
