<template>
    <div class="p-8 mx-auto max-w-2xl">
        <h2 class="text-3xl font-bold mb-6 text-primary-500">
            👤 顧客情報の編集
        </h2>

        <form @submit.prevent="updateCustomer" class="space-y-6">
            <div>
                <label class="block font-semibold mb-1">名前</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="w-full border px-4 py-2 rounded"
                    required
                />
            </div>

            <div>
                <label class="block font-semibold mb-1">電話番号</label>
                <input
                    v-model="form.phone"
                    type="tel"
                    class="w-full border px-4 py-2 rounded"
                />
            </div>

            <div>
                <label class="block font-semibold mb-1">メールアドレス</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="w-full border px-4 py-2 rounded"
                />
            </div>

            <div>
                <label class="block font-semibold mb-1"
                    >アレルギーの有無・内容</label
                >
                <textarea
                    v-model="form.allergy_notes"
                    rows="3"
                    class="w-full border px-4 py-2 rounded"
                ></textarea>
            </div>

            <div>
                <label class="block font-semibold mb-1"
                    >手術歴の有無・内容</label
                >
                <textarea
                    v-model="form.surgery_notes"
                    rows="3"
                    class="w-full border px-4 py-2 rounded"
                ></textarea>
            </div>

            <div class="flex gap-4 mt-6">
                <button
                    type="submit"
                    class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-2xl shadow"
                >
                    更新する
                </button>
                <RouterLink
                    :to="{ name: 'shop.customer.index' }"
                    class="bg-greige-300 hover:bg-greige-400 text-gray-800 font-semibold py-2 px-4 rounded-2xl shadow"
                >
                    キャンセル
                </RouterLink>
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
    phone: "",
    email: "",
    allergy_notes: "",
    surgery_notes: "",
});

const fetchCustomer = async () => {
    const res = await axios.get(`/api/shop/customers/${route.params.id}`);
    form.value = res.data;
};

const updateCustomer = async () => {
    await axios.patch(`/api/shop/customers/${route.params.id}`, form.value);
    router.push({ name: "shop.customer.index" });
};

onMounted(fetchCustomer);
</script>
