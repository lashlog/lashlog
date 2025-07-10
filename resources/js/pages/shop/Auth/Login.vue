<template>
    <div class="min-h-screen flex items-center justify-center bg-greige-50">
        <div class="w-full max-w-sm bg-white p-8 shadow-xl rounded-2xl">
            <h2 class="text-2xl font-bold text-center text-greige-700 mb-6">
                Lash Log 管理画面
            </h2>

            <form @submit.prevent="handleLogin" class="space-y-4">
                <div>
                    <label
                        for="email"
                        class="block text-sm text-greige-700 mb-1"
                        >メールアドレス</label
                    >
                    <input
                        v-model="email"
                        id="email"
                        type="email"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-300"
                    />
                </div>

                <div>
                    <label
                        for="password"
                        class="block text-sm text-greige-700 mb-1"
                        >パスワード</label
                    >
                    <input
                        v-model="password"
                        id="password"
                        type="password"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-300"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-md transition"
                >
                    ログイン
                </button>

                <p v-if="error" class="text-red-500 text-sm text-center">
                    {{ error }}
                </p>
            </form>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { useShopAuth } from "@/composables/useShopAuth";
import { useShopStore } from "@/stores/shop";
const shopStore = useShopStore();
const { fetchShop } = useShopAuth();

const router = useRouter();
const email = ref("");
const password = ref("");
const error = ref("");

onMounted(async () => {
    await fetchShop();
    if (shopStore.shop) {
        router.push("/shop/calendar");
    }
});
const handleLogin = async () => {
    error.value = "";
    try {
        await axios.get("api/sanctum/csrf-cookie", { withCredentials: true });
        await axios.post(
            "api/shop/login",
            {
                email: email.value,
                password: password.value,
            },
            { withCredentials: true }
        );
        // await axios.get("/api/shop/me", { withCredentials: true });
        await fetchShop(); // 👈 ログイン後にショップ情報を再取得
        router.push("/calendar");
    } catch {
        error.value =
            "ログインに失敗しました。メールアドレスまたはパスワードをご確認ください。";
    }
};
</script>
