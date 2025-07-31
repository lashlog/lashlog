<!-- resources/js/pages/shop/reservation/EditReservationModal.vue -->
<template>
    <div
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-2xl shadow-xl w-[400px] p-6">
            <h2 class="text-xl font-bold text-center mb-6">
                {{ reservation.id ? "予約の編集" : "新規予約" }}
            </h2>

            <!-- お名前 -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">お名前</label>
                <Multiselect
                    v-model="form.customer"
                    :options="customerList"
                    label="name"
                    track-by="id"
                    placeholder="顧客名を選択または入力"
                    select-label="選択"
                    deselect-label="削除"
                    selected-label="選択済み"
                    no-result="該当する顧客が見つかりません"
                >
                    <template #noResult>
                        <span class="px-2 py-1 text-sm text-gray-500"
                            >該当する顧客は見つかりませんでした</span
                        >
                    </template>
                    <template #noOptions>
                        <span class="px-2 py-1 text-sm text-gray-500"
                            >顧客が存在しません</span
                        >
                    </template>
                </Multiselect>
                <button
                    @click="openCustomerModal"
                    class="bg-primary-500 text-white px-3 py-1 rounded"
                >
                    ＋ 新規顧客を登録
                </button>

                <!-- 顧客登録モーダル -->
                <CustomerCreateModal
                    ref="customerModalRef"
                    @created="handleCustomerCreated"
                />
            </div>

            <!-- 新規顧客名入力欄（customer_id が 0 のときのみ表示） -->
            <div v-if="form.customer_id == 0" class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1"
                    >お名前</label
                >
                <input
                    type="text"
                    v-model="form.customer_name"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="お名前を入力"
                />
            </div>

            <!-- メニュー -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1"
                    >メニュー</label
                >
                <select
                    v-model="form.menu_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option disabled value="">選択してください</option>
                    <option
                        v-for="menu in menuList"
                        :key="menu.id"
                        :value="menu.id"
                    >
                        {{ menu.name }}
                    </option>
                </select>
            </div>

            <!-- 開始時間 -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1"
                    >開始時間</label
                >
                <template>
                    <LabeledInput label="開始時間">
                        <select
                            v-model="form.start_time"
                            class="border p-2 rounded w-full"
                        >
                            <option
                                v-for="time in timeOptions"
                                :key="time"
                                :value="time"
                            >
                                {{ time }}
                            </option>
                        </select>
                    </LabeledInput>
                </template>
            </div>

            <!-- 終了時間 -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1"
                    >終了時間</label
                >
                <input
                    type="time"
                    v-model="form.end_time"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1"
                    >予約媒体</label
                >
                <select
                    v-model="form.reservation_source_id"
                    class="w-full p-2 border rounded"
                >
                    <option disabled value="">選択してください</option>
                    <option
                        v-for="source in reservationSources"
                        :key="source.id"
                        :value="source.id"
                    >
                        {{ source.name }}
                    </option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1"
                    >ステータス</label
                >
                <select
                    v-model="form.reservation_status"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="confirmed">確定</option>
                    <option value="canceled">キャンセル</option>
                    <option value="no_show">無断キャンセル</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1"
                    >メモ</label
                >
                <textarea
                    v-model="form.memo"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    rows="3"
                    placeholder="メモ（任意）"
                />
            </div>
            <!-- ボタン -->
            <div class="mt-6 flex justify-end gap-3">
                <button
                    class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-sm"
                    @click="$emit('close')"
                >
                    閉じる
                </button>
                <button
                    class="px-4 py-2 rounded bg-primary-500 hover:bg-primary-600 text-white text-sm"
                    @click="save"
                >
                    保存
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from "vue";
import axios from "axios";
import CustomerCreateModal from "../customer/CustomerCreateModal.vue";
import { useShopStore } from "@/stores/shop";
const shopStore = useShopStore();
const shop = computed(() => shopStore.shop);

const props = defineProps({
    reservation: Object,
    reservationList: Array,
    handleCustomerCreated: Function,
    currentDate: String,
    availableStartTimes: Array, // 営業時間情報
});
const emit = defineEmits(["close", "saved"]);
const customers = ref([]);
const form = ref({
    customer_id: null,
    customer_name: "",
    menu_id: null,
    start_time: "",
    end_time: "",
    reservation_status: "confirmed", // ← 追加
    reservation_source_id: "",
    memo: "",
});
const customerList = ref([]);
const reservationSources = ref([]);
const interval = shop.slotMinutes ? shop.slotMinutes : 30; // 予約単位（分単位）
const timeOptions = computed(() =>
    generateTimeOptions(
        interval,
        props.businessHours.open_time,
        props.businessHours.close_time
    )
);

function generateTimeOptions(unitMinutes = 30, start = "09:00", end = "23:00") {
    const times = [];
    const [startHour, startMin] = start.split(":").map(Number);
    const [endHour, endMin] = end.split(":").map(Number);
    console.log("generateTimeOptions", startHour, startMin, endHour, endMin);
    console.log("props.currentDate", props.currentDate);
    let current = new Date(props.currentDate);
    current.setHours(startHour, startMin, 0, 0);

    const endTime = new Date(props.currentDate);
    endTime.setHours(endHour, endMin, 0, 0);

    while (current <= endTime) {
        const hours = current.getHours().toString().padStart(2, "0");
        const minutes = current.getMinutes().toString().padStart(2, "0");
        times.push(`${hours}:${minutes}`);
        current.setMinutes(current.getMinutes() + unitMinutes);
    }
    console.log("生成された時間オプション:", times);
    return times;
}
const fetchReservationSources = async () => {
    try {
        const res = await axios.get("/api/shop/reservation-sources");
        reservationSources.value = res.data.filter((s) => s.is_active);
    } catch (e) {
        console.error("予約媒体の取得に失敗しました", e);
    }
};

const addNewCustomer = (newName) => {
    form.value.customer = { id: null, name: newName };
};
const fetchCustomers = async () => {
    const res = await axios.get("/api/shop/customers", {
        withCredentials: true,
    });
    customerList.value = res.data;
};
const menuList = ref([]);

const fetchMenus = async () => {
    const res = await axios.get("/api/shop/menus", { withCredentials: true });
    menuList.value = res.data;
};

onMounted(() => {
    fetchReservationSources();
    fetchCustomers();
    fetchMenus();
});
const getSourceName = (id) => {
    const source = reservationSources.value.find((s) => s.id === id);
    return source ? source.name : "（媒体不明）";
};
watch(
    () => props.reservation,
    (res) => {
        if (res) {
            form.value.customer = res.customer
                ? { id: res.customer.id, name: res.customer.name }
                : null;
            form.value.customer_name = res.customer?.name || "";
            form.value.menu_id = res.menu?.id ?? null;
            form.value.start_time = res.start_time;
            form.value.end_time = res.end_time;
            // 追加分
            form.value.reservation_status =
                res.reservation_status || "confirmed";
            form.value.memo = res.memo || "";
            form.value.reservation_source_id = res.reservation_source_id || "";
        }
    },
    { immediate: true }
);
const formatTime = (timeStr) => {
    return timeStr.slice(0, 5); // "10:00:00" → "10:00"
};
const isOverlapping = () => {
    const format = (t) => t?.slice(0, 5);
    const newStart = form.value.start_time;
    const newEnd = form.value.end_time;
    const staffId = props.reservation.staff_id;
    const reservedDate = props.reservation.reserved_date;
    const selfId = props.reservation.id;
    // 自分以外の予約に対して重複チェック
    console.log("Checking overlap for staffId:", staffId);
    console.log("Reserved date:", reservedDate);
    console.log("New start:", newStart);
    console.log("New end:", newEnd);
    const overlapping =
        props.reservationList?.filter((r) => {
            const rStart = format(r.start_time);
            const rEnd = format(r.end_time);
            return (
                r.staff_id === staffId &&
                r.reserved_date === reservedDate &&
                r.id !== props.reservation.id &&
                newStart < rEnd &&
                newEnd > rStart
            );
        }).length > 0;

    return overlapping;
};
const save = async () => {
    if (isOverlapping()) {
        alert("この時間帯はすでに予約があります。");
        return;
    }
    try {
        const selectedCustomer = form.value.customer;
        let customerId = selectedCustomer?.id;
        let customerName = selectedCustomer?.name;
        form.value.start_time = formatTime(form.value.start_time);
        form.value.end_time = formatTime(form.value.end_time);
        // 顧客が未登録なら新規登録
        if (!customerId) {
            const res = await axios.post("/api/shop/customers", {
                name: customerName,
            });
            customerId = res.data.id;
        }
        const payload = {
            customer_id: customerId,
            menu_id: form.value.menu_id,
            start_time: form.value.start_time,
            end_time: form.value.end_time,
            reserved_date: props.reservation.reserved_date,
            staff_id: props.reservation.staff_id,
            reservation_status: form.value.reservation_status,
            reservation_source_id: form.value.reservation_source_id,
            memo: form.value.memo,
        };

        if (!props.reservation.id) {
            await axios.post("/api/shop/reservations", payload, {
                withCredentials: true,
            });
        } else {
            await axios.put(
                `/api/shop/reservations/${props.reservation.id}`,
                payload,
                { withCredentials: true }
            );
        }

        emit("saved");
        emit("close");
    } catch (error) {
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            console.log("バリデーションエラー", errors);

            // 例：1件目のエラーを表示する
            alert(Object.values(errors)[0][0]);
        } else {
            console.error("その他のエラー", error);
        }
    }
};
const customerModalRef = ref(null);

const openCustomerModal = () => {
    console.log("モーダル開く");
    customerModalRef.value?.open();
};
</script>
