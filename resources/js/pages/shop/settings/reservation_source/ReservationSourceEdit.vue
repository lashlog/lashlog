<template>
    <div class="p-4">
        <h2 class="text-xl font-bold mb-4">予約元媒体編集</h2>

        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block mb-1 text-sm font-semibold">表示名</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="w-full p-2 border rounded"
                    :class="{ 'border-red-500': errors.name }"
                />
                <p v-if="errors.name" class="text-red-500 text-sm mt-1">
                    {{ errors.name }}
                </p>
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

            <div class="flex space-x-4">
                <button
                    type="submit"
                    class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-2xl shadow"
                >
                    更新する
                </button>
                <router-link
                    to="/shop/settings/reservation-sources"
                    class="bg-greige-300 hover:bg-greige-400 text-gray-800 font-semibold py-2 px-4 rounded-2xl shadow"
                >
                    キャンセル
                </router-link>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();
const form = ref({
    name: "",
    is_active: true,
});
const errors = ref({});
const fees = ref([{ min_amount: null, max_amount: null, fee_type: "fixed", fee_value: 0 }]);
const feesError = ref("");

const load = async () => {
    try {
        const res = await axios.get(
            `/api/shop/reservation-sources/${route.params.id}`
        );
        form.value = {
            name: res.data.name,
            is_active: Boolean(res.data.is_active),
        };
        const feeRes = await axios.get(
            `/api/shop/reservation-sources/${route.params.id}/fees`
        );
        fees.value = feeRes.data.length
            ? feeRes.data
            : [{ min_amount: null, max_amount: null, fee_type: "fixed", fee_value: 0 }];
    } catch (e) {
        console.error(e);
        alert("取得に失敗しました");
    }
};

const submit = async () => {
    try {
        await axios.put(
            `/api/shop/reservation-sources/${route.params.id}`,
            form.value
        );
        await axios.post(
            `/api/shop/reservation-sources/${route.params.id}/fees`,
            { fees: fees.value }
        );
        router.push("/shop/settings/reservation-sources");
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors || {};
            feesError.value = e.response.data.errors?.fees?.[0] || "";
        } else {
            console.error(e);
            alert("更新に失敗しました");
        }
    }
};

const addFee = () => {
    fees.value.push({ min_amount: null, max_amount: null, fee_type: "fixed", fee_value: 0 });
};

const removeFee = (index) => {
    fees.value.splice(index, 1);
};

onMounted(load);
</script>
