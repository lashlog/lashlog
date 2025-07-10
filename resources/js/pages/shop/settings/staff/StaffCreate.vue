<template>
    <FormWrapper>
        <h2 class="text-3xl font-bold mb-6 text-primary-500">
            👤 スタッフ追加
        </h2>

        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block mb-1 font-medium">名前</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="w-full border rounded px-4 py-2"
                    required
                />
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">メールアドレス</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="w-full border rounded px-4 py-2"
                    required
                />
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">パスワード</label>
                <input
                    v-model="form.password"
                    type="password"
                    class="w-full border rounded px-4 py-2"
                    required
                />
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-medium">パスワード(確認)</label>
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    class="w-full border rounded px-4 py-2"
                    required
                />
            </div>

            <div class="mb-6">
                <label class="block mb-1 font-medium">権限</label>
                <select
                    v-model="form.role"
                    class="w-full border rounded px-4 py-2"
                >
                    <option value="staff">スタッフ</option>
                    <option value="owner">オーナー</option>
                </select>
            </div>

            <div class="flex gap-4">
                <button
                    type="submit"
                    class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-2xl shadow"
                >
                    登録
                </button>
                <button
                    type="button"
                    @click="goBack"
                    class="bg-greige-300 hover:bg-greige-400 text-gray-800 font-semibold py-2 px-4 rounded-2xl shadow"
                >
                    キャンセル
                </button>
            </div>
        </form>
    </FormWrapper>
</template>

<script setup>
import { ref } from "vue";
import FormWrapper from "@/components/FormWrapper.vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

const form = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "staff",
});

const submit = async () => {
    try {
        await axios.post("/api/shop/staffs", form.value);
        alert("スタッフを追加しました");
        router.push("/shop/settings/staffs");
    } catch (e) {
        alert("登録に失敗しました");
        console.error(e);
    }
};
const goBack = () => {
    router.push("/shop/settings/staffs");
};
</script>
