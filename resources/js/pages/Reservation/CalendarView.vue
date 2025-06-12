<template>
    <div>
        <div>
            <button @click="view = 'week'">週表示</button>
            <button @click="view = 'day'">日表示</button>
        </div>
        <button @click="openCreateModal" class="btn btn-create">
            新規予約作成
        </button>

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
                        <button
                            @click.stop="removeReservation(res.id)"
                            class="btn-delete"
                        >
                            ×
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 編集モーダル部分をここに入れる -->
    <div v-if="isOpen" class="modal-overlay">
        <div class="modal-content">
            <h3 class="modal-title">予約編集</h3>

            <div class="modal-field">
                <label>顧客名</label>
                <div class="modal-text">
                    <select
                        v-model="editingReservation.customerId"
                        class="modal-input"
                    >
                        <option
                            v-for="customer in customerList"
                            :value="customer.id"
                            :key="customer.id"
                        >
                            {{ customer.name }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="modal-field">
                <label>開始時間</label>
                <input
                    v-model="editingReservation.startTime"
                    type="time"
                    class="modal-input"
                />
            </div>

            <div class="modal-field">
                <label>終了時間</label>
                <input
                    v-model="editingReservation.endTime"
                    type="time"
                    class="modal-input"
                />
            </div>

            <div class="modal-field">
                <label>所要時間（分）</label>
                <input
                    v-model.number="editingReservation.durationMinutes"
                    type="number"
                    min="15"
                    step="15"
                    class="modal-input"
                />
            </div>

            <div class="modal-actions">
                <button @click="saveChanges" class="btn btn-save">保存</button>
                <button @click="close" class="btn btn-cancel">閉じる</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import axios from "../../axios";

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
const customerList = ref([]);
async function loadCustomers() {
    const res = await fetch("/api/customers");
    customerList.value = await res.json();
}

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
        top: `${(startM - baseMinutes) * heightPerMin}px`,
        height: `${(endM - startM) * heightPerMin}px`,
        left: "2px",
        right: "2px",
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

// function openEditModal(res) {
//     editingReservation.value = { ...res }; // コピーを渡す
//     isOpen.value = true;
// }

function close() {
    isOpen.value = false;
}
async function saveChanges() {
    const payload = {
        customer_id: editingReservation.value.customerId, // ← 注意：IDを使う
        menu_id: 1,
        staff_id: editingReservation.value.staffId,
        reserved_date: "2024-06-01",
        start_time: editingReservation.value.startTime,
        end_time: editingReservation.value.endTime,
        duration_minutes: editingReservation.value.durationMinutes,
    };

    try {
        let res;
        if (isEditing.value) {
            res = await updateReservation(editingReservation.value.id, payload);
        } else {
            res = await createReservation(payload);
            reservations.value.push({
                id: res.reservation.id,
                staffId: res.reservation.staff_id,
                startTime: res.reservation.start_time,
                endTime: res.reservation.end_time,
                customer: res.reservation.customer.name,
            });
        }
        alert(isEditing.value ? "更新しました！" : "作成しました！");
        isOpen.value = false;
    } catch (error) {
        alert("保存失敗しました: " + error.message);
    }
}
async function createNewReservation() {
    const payload = {
        customer_id: 1,
        menu_id: 1,
        staff_id: 1,
        reserved_date: "2024-06-01",
        start_time: "10:00",
        end_time: "11:30",
        duration_minutes: 90,
    };
    try {
        const res = await createReservation(payload);
        reservations.value.push({
            id: res.reservation.id,
            staffId: res.reservation.staff_id,
            startTime: res.reservation.start_time,
            endTime: res.reservation.end_time,
            customer: res.reservation.customer.name,
        });
        alert("新規予約を作成しました！");
    } catch (error) {
        alert("作成失敗: " + error.message);
    }
}

async function removeReservation(id) {
    try {
        await deleteReservation(id);
        reservations.value = reservations.value.filter((res) => res.id !== id);
        alert("予約を削除しました！");
    } catch (error) {
        alert("削除失敗: " + error.message);
    }
}
async function updateReservation(id, payload) {
    const response = await axios.patch(`/reservations/${id}`, payload);
    return response.data;
}

async function createReservation(payload) {
    const response = await axios.post(`/reservations`, payload);
    return response.data;
}

async function deleteReservation(id) {
    const response = await axios.delete(`/reservations/${id}`);
    return response.data;
}
const isEditing = ref(false);

function openCreateModal() {
    isEditing.value = false;
    editingReservation.value = {
        customer: "",
        customerId: customerList.value[0]?.id || null, // 先頭の顧客を初期選択
        staffId: staffList.value[0].id,
        startTime: "09:00",
        endTime: "10:30",
        durationMinutes: 90,
    };
    isOpen.value = true;
}

function openEditModal(res) {
    isEditing.value = true;
    editingReservation.value = {
        ...res,
        customerId: res.customerId || findCustomerIdByName(res.customer),
    };
    isOpen.value = true;
}
function findCustomerIdByName(name) {
    const customer = customerList.value.find((c) => c.name === name);
    return customer ? customer.id : null;
}
</script>

<style scoped></style>
