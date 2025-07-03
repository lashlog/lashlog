<template>
    <div class="relative mb-10">
        <div
            class="text-xs mt-1 right-8 px-2 py-1 bg-white rounded absolute"
            :class="
                schedule[formatDate(day)]?.length
                    ? 'text-green-600'
                    : 'text-gray-400'
            "
        >
            {{
                schedule[formatDate(day)]?.length
                    ? `${schedule[formatDate(day)].length} 件登録済`
                    : "登録なし"
            }}
        </div>
    </div>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow">
        <div class="flex items-center justify-between mb-4">
            <button
                @click="prevMonth"
                class="text-primary-600 hover:cursor-pointer"
            >
                ← 前の月
            </button>
            <h2 class="text-xl font-bold">
                {{ currentYear }}年 {{ currentMonth + 1 }}月 のスケジュール
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
                class="p-3 border min-h-24 rounded"
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
                    v-if="schedule[formatDate(day)]?.length"
                    class="text-xs text-green-600 mt-1"
                >
                    {{ schedule[formatDate(day)].length }} 件登録済
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
import { ref, watch, computed } from "vue";

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({}),
    },
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
    selectedDay.value = day;
    selectedSchedule.value = Array.isArray(schedule.value[formatDate(day)])
        ? [...schedule.value[formatDate(day)]]
        : [];
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

const saveSchedule = () => {
    schedule.value[formatDate(selectedDay.value)] =
        selectedSchedule.value.filter((s) => s.start && s.end);
    emit("update:modelValue", { ...schedule.value });
    closeModal();
};
const isPastDay = (day) => {
    const today = new Date();
    today.setHours(0, 0, 0, 0); // 時間をリセット（正確な日付比較のため）

    const date = new Date(currentYear.value, currentMonth.value, day);
    return date < today;
};
</script>

<style scoped></style>
