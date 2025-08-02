<template>
    <div class="p-6 max-w-xl mx-auto">
        <h2 class="text-xl font-bold mb-4 text-primary-700">媒体を新規作成</h2>

        <form @submit.prevent="handleSubmit">
            <div class="mb-4">
                <label for="name" class="block mb-1 text-sm font-medium"
                    >媒体名</label
                >
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full border rounded px-3 py-2"
                />
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        v-model="form.is_active"
                        class="mr-2"
                    />
                    有効
                </label>
            </div>

            <div class="mb-4">
                <h3 class="font-semibold mb-2">手数料ルール</h3>
                <table class="w-full mb-2 text-sm">
                    <thead>
                        <tr>
                            <th class="p-2 border">最小金額</th>
                            <th class="p-2 border">最大金額</th>
                            <th class="p-2 border">種別</th>
                            <th class="p-2 border">手数料</th>
                            <th class="p-2 border"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(fee, index) in fees" :key="index">
                            <td class="p-2 border">
                                <input type="number" v-model.number="fee.min_amount" class="w-full p-1 border rounded" />
                            </td>
                            <td class="p-2 border">
                                <input type="number" v-model.number="fee.max_amount" class="w-full p-1 border rounded" />
                            </td>
                            <td class="p-2 border">
                                <select v-model="fee.fee_type" class="w-full p-1 border rounded">
                                    <option value="fixed">固定</option>
                                    <option value="percent">%</option>
                                </select>
                            </td>
                            <td class="p-2 border">
                                <input type="number" v-model.number="fee.fee_value" class="w-full p-1 border rounded" />
                            </td>
                            <td class="p-2 border text-center">
                                <button type="button" @click="removeFee(index)" class="text-red-500">削除</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" @click="addFee" class="bg-green-500 text-white px-2 py-1 rounded">手数料ルールを追加</button>
                <p v-if="feesError" class="text-red-500 text-sm mt-1">{{ feesError }}</p>
            </div>

            <div class="flex justify-between">
                <button
                    type="submit"
                    class="bg-primary-500 text-white px-4 py-2 rounded hover:opacity-90"
                >
                    登録する
                </button>
                <RouterLink
                    :to="{ name: 'shop.settings.reservation_sources' }"
                    class="text-sm text-gray-600 underline"
                >
                    一覧に戻る
                </RouterLink>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { useShopStore } from "@/stores/shop";

const router = useRouter();
const shopStore = useShopStore();

const form = reactive({
    name: "",
    is_active: true,
});
const fees = ref([
    { min_amount: null, max_amount: null, fee_type: "fixed", fee_value: 0 },
]);
const feesError = ref("");

const handleSubmit = async () => {
    try {
        const res = await axios.post("/api/shop/reservation-sources", {
            ...form,
            shop_id: shopStore.shop.id,
        });
        await axios.post(
            `/api/shop/reservation-sources/${res.data.id}/fees`,
            { fees: fees.value }
        );
        router.push({ name: "shop.settings.reservation_sources" });
    } catch (error) {
        if (error.response?.status === 422) {
            feesError.value = error.response.data.errors?.fees?.[0] || "";
        }
        alert("登録に失敗しました");
        console.error(error);
    }
};

const addFee = () => {
    fees.value.push({ min_amount: null, max_amount: null, fee_type: "fixed", fee_value: 0 });
};

const removeFee = (index) => {
    fees.value.splice(index, 1);
};
</script>
