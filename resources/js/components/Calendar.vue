<template>
    <div>
        <div>
            <button @click="view = 'week'">週表示</button>
            <button @click="view = 'day'">日表示</button>
        </div>

        <div class="calendar">
            <!-- 時間目盛り列 -->
            <div class="time-column">
                <div class="staff-header">時間</div>

                <div
                    class="time-slot"
                    v-for="time in timeSlots"
                    :key="time"
                    :style="{ height: timeSlotHeight }"
                >
                    {{ time }}
                </div>
            </div>

            <!-- スタッフ列 -->
            <div
                class="staff-column"
                v-for="staff in staffList"
                :key="staff.id"
            >
                <div class="staff-header">{{ staff.name }}</div>
                <div
                    class="staff-body"
                    @dragover.prevent
                    @drop="onDrop(staff.id, $event)"
                >
                    <!-- 背景の時間枠 -->
                    <div
                        v-for="time in timeSlots"
                        :key="time"
                        class="time-slot"
                        :style="{ height: timeSlotHeight }"
                    ></div>

                    <!-- 絶対配置の予約ブロック -->
                    <div
                        v-for="res in getReservations(staff.id)"
                        :key="res.id"
                        class="reservation"
                        :style="calcStyle(res)"
                        draggable="true"
                        @dragstart="onDragStart(res)"
                        @dblclick="openEditModal(res)"
                    >
                        {{ res.customer }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 編集モーダル部分をここに入れる -->
    <div v-if="isOpen" class="modal">
        <h3>予約編集</h3>
        <div>顧客名: {{ editingReservation.customer }}</div>
        <div>
            開始時間:
            <input v-model="editingReservation.startTime" type="time" />
        </div>
        <div>
            終了時間:
            <input v-model="editingReservation.endTime" type="time" />
        </div>
        <div>
            所要時間:
            <input
                v-model.number="editingReservation.durationMinutes"
                type="number"
                min="15"
                step="15"
            />
        </div>
        <button @click="saveChanges">保存</button>
        <button @click="close">閉じる</button>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
const timeStep = ref(30); // ← ここを 15, 30, 60 に切り替え可能
const view = ref("day");

const staffList = ref([
    { id: 1, name: "山田" },
    { id: 2, name: "佐藤" },
    { id: 3, name: "鈴木" },
]);

const timeSlots = computed(() => {
    const slots = [];
    for (let h = 9; h <= 18; h++) {
        for (let m = 0; m < 60; m += timeStep.value) {
            slots.push(
                `${String(h).padStart(2, "0")}:${String(m).padStart(2, "0")}`
            );
        }
    }
    return slots;
});

const timeSlotHeight = computed(() => `${timeStep.value * 2}px`);
const reservations = ref([
    {
        id: 1,
        staffId: 1,
        startTime: "09:00",
        endTime: "10:30",
        customer: "田中",
    },
    {
        id: 2,
        staffId: 2,
        startTime: "10:00",
        endTime: "11:30",
        customer: "高橋",
    },
    {
        id: 3,
        staffId: 1,
        startTime: "11:00",
        endTime: "12:30",
        customer: "佐々木",
    },
    {
        id: 4,
        staffId: 3,
        startTime: "09:30",
        endTime: "11:00",
        customer: "中村",
    },
]);

function toMinutes(timeStr) {
    const [h, m] = timeStr.split(":").map(Number);
    return h * 60 + m;
}

function calcStyle(res) {
    const baseMinutes = 9 * 60;
    const heightPerMin = 2; // 1分あたり2px

    const startM = toMinutes(res.startTime);
    const endM = toMinutes(res.endTime);

    return {
        position: "absolute",
        top: `${(startM - baseMinutes) * heightPerMin}px`,
        height: `${(endM - startM) * heightPerMin}px`,
        left: "2px",
        right: "2px",
        background: "#a3d5ff",
        border: "1px solid #007bff",
        boxSizing: "border-box",
        padding: "2px",
        overflow: "hidden",
        borderRadius: "4px",
        fontSize: "12px",
    };
}

function getReservations(staffId) {
    return reservations.value.filter((res) => res.staffId === staffId);
}

const draggingReservation = ref(null);
const dragOffsetY = ref(0);
function onDragStart(res) {
    draggingReservation.value = res;
    const elementRect = event.target.getBoundingClientRect();
    dragOffsetY.value = event.clientY - elementRect.top;
    draggingReservation.value = res;
}

function onDrop(newStaffId, event) {
    const staffBodyRect = event.currentTarget.getBoundingClientRect();
    const elementTop = event.clientY - dragOffsetY.value;
    const relativeY = elementTop - staffBodyRect.top;

    const baseMinutes = 9 * 60;
    const minutesPerPx = 0.5;
    const step = timeStep.value;

    const dropMinutes = Math.floor(relativeY * minutesPerPx);
    const snappedMinutes = Math.floor(dropMinutes / step) * step;
    let newStartMinutes = baseMinutes + snappedMinutes;

    const duration =
        toMinutes(draggingReservation.value.endTime) -
        toMinutes(draggingReservation.value.startTime);
    let newEndMinutes = newStartMinutes + duration;

    if (newStartMinutes < baseMinutes) {
        newStartMinutes = baseMinutes;
        newEndMinutes = baseMinutes + duration;
    }
    if (newEndMinutes > 18 * 60) {
        newEndMinutes = 18 * 60;
        newStartMinutes = newEndMinutes - duration;
    }

    const newRes = {
        ...draggingReservation.value,
        staffId: newStaffId,
        startTime: minutesToTime(newStartMinutes),
        endTime: minutesToTime(newEndMinutes),
    };

    if (isOverlapping(newRes, newStaffId)) {
        alert("この時間帯は他の予約と重複しています。");
        draggingReservation.value = null;
        return;
    }

    draggingReservation.value.staffId = newStaffId;
    draggingReservation.value.startTime = newRes.startTime;
    draggingReservation.value.endTime = newRes.endTime;
    draggingReservation.value = null;
}
function minutesToTime(minutes) {
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
    return `${String(h).padStart(2, "0")}:${String(m).padStart(2, "0")}`;
}
function isOverlapping(newRes, staffId) {
    return reservations.value.some((res) => {
        if (res.staffId !== staffId || res.id === newRes.id) return false;

        const startA = toMinutes(res.startTime);
        const endA = toMinutes(res.endTime);
        const startB = toMinutes(newRes.startTime);
        const endB = toMinutes(newRes.endTime);

        return startA < endB && startB < endA;
    });
}
const isOpen = ref(false);
const editingReservation = ref({});

function openEditModal(res) {
    editingReservation.value = { ...res }; // コピーを渡す
    isOpen.value = true;
}

function close() {
    isOpen.value = false;
}

function saveChanges() {
    const idx = reservations.value.findIndex(
        (r) => r.id === editingReservation.value.id
    );
    if (
        isOverlapping(
            editingReservation.value,
            editingReservation.value.staffId
        )
    ) {
        alert("他の予約と重なります！");
        return;
    }
    if (editingReservation.value.startTime > editingReservation.value.endTime) {
        alert("開始時間は終了時間より前にしてください。");
        return;
    }
    if (idx !== -1) {
        reservations.value[idx] = { ...editingReservation.value };
        isOpen.value = false;
    }
}
</script>

<style scoped></style>
