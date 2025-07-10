<!-- pages/shop/customers/CustomerCreate.vue -->
<template>
    <div class="p-8 mt-2 mr-0 w-full max-w-2xl">
        <h2 class="text-3xl font-bold mb-6 text-primary-500">👤 顧客登録</h2>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label class="block mb-1 font-semibold"
                    >名前 <span class="text-red-500">*</span></label
                >
                <input
                    type="text"
                    v-model="form.name"
                    class="w-full border border-gray-300 rounded px-4 py-2"
                    required
                />
            </div>

            <div>
                <label class="block mb-1 font-semibold">電話番号</label>
                <input
                    type="tel"
                    v-model="form.phone"
                    class="w-full border border-gray-300 rounded px-4 py-2"
                />
            </div>

            <div>
                <label class="block mb-1 font-semibold">メールアドレス</label>
                <input
                    type="email"
                    v-model="form.email"
                    class="w-full border border-gray-300 rounded px-4 py-2"
                />
            </div>

            <div>
                <label class="block mb-1 font-semibold"
                    >アレルギーに関するメモ</label
                >
                <textarea
                    v-model="form.allergy_notes"
                    class="w-full border border-gray-300 rounded px-4 py-2"
                    rows="3"
                ></textarea>
            </div>

            <div>
                <label class="block mb-1 font-semibold"
                    >手術歴に関するメモ</label
                >
                <textarea
                    v-model="form.surgery_notes"
                    class="w-full border border-gray-300 rounded px-4 py-2"
                    rows="3"
                ></textarea>
            </div>

            <div class="flex gap-4 mt-6">
                <button
                    type="submit"
                    class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-2xl shadow"
                >
                    登録する
                </button>
                <RouterLink
                    to="/shop/customers"
                    class="bg-greige-300 hover:bg-greige-400 text-gray-800 font-semibold py-2 px-4 rounded-2xl shadow"
                >
                    キャンセル
                </RouterLink>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

const form = ref({
    name: "",
    phone: "",
    email: "",
    allergy_notes: "",
    surgery_notes: "",
});

const submit = async () => {
    try {
        await axios.post("/api/shop/customers", form.value);
        router.push("/shop/customers");
    } catch (err) {
        alert("登録に失敗しました");
        console.error(err);
    }
};
</script>
