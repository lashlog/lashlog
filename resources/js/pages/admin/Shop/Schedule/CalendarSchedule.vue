<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <button @click="prevMonth" class="text-blue-500">← 前の月</button>
            <h2 class="text-xl font-bold">
                {{ currentYear }}年 {{ currentMonth + 1 }}月 のスケジュール
            </h2>
            <button @click="nextMonth" class="text-blue-500">次の月 →</button>
        </div>

        <div class="grid grid-cols-7 gap-2">
            <div
                v-for="day in daysInMonth"
                :key="day"
                @click="openModal(day)"
                class="p-3 border rounded cursor-pointer hover:bg-gray-100"
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
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
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
    return Array.from({ length: days }, (_, i) => i + 1);
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
</script>

<style scoped></style>
