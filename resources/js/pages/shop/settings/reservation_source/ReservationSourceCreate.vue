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
import { reactive } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { useShopStore } from "@/stores/shop";

const router = useRouter();
const shopStore = useShopStore();

const form = reactive({
    name: "",
    is_active: true,
});

const handleSubmit = async () => {
    try {
        await axios.post("/api/shop/reservation-sources", {
            ...form,
            shop_id: shopStore.shop.id,
        });
        router.push({ name: "shop.settings.reservation_sources" });
    } catch (error) {
        alert("登録に失敗しました");
        console.error(error);
    }
};
</script>
