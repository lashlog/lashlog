<template>
    <FormWrapper>
        <h2 class="text-3xl font-bold mb-6 text-primary-500">
            🏠 店舗基本情報
        </h2>

        <form @submit.prevent="submit">
            <!-- 店舗名 -->
            <LabeledInput label="店舗名" v-model="form.name" />

            <!-- 電話番号 -->
            <LabeledInput label="電話番号" v-model="form.phone" />

            <!-- 住所 -->
            <LabeledInput label="住所" v-model="form.address" />

            <!-- Google Map URL -->
            <LabeledInput label="Google Map URL" v-model="form.map_url" />

            <!-- Instagram URL -->
            <LabeledInput label="Instagram URL" v-model="form.instagram_url" />

            <!-- LIFF URL 表示（readonly） -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >LINE予約用URL</label
                >
                <div class="flex items-center">
                    <input
                        type="text"
                        :value="generatedLiffUrl"
                        readonly
                        class="w-full p-2 border rounded bg-gray-100 text-sm"
                    />
                    <button
                        @click="copyLiffUrl"
                        type="button"
                        class="ml-2 text-primary-500 hover:text-primary-700"
                        title="コピー"
                    >
                        <!-- lucide-react の Copy アイコンなど -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 10h6a2 2 0 002-2v-6a2 2 0 00-2-2h-6a2 2 0 00-2 2v6a2 2 0 002 2z"
                            />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    ※
                    このURLをLINE公式アカウントのリッチメニューに貼ってください
                </p>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        予約単位時間（分）
                    </label>
                    <select
                        v-model="form.slot_minutes"
                        class="w-full p-2 border rounded text-sm"
                    >
                        <option :value="15">15分</option>
                        <option :value="30">30分</option>
                        <option :value="60">60分</option>
                    </select>
                </div>
            </div>

            <!-- 保存ボタン -->
            <div class="mt-6 flex justify-end">
                <button
                    type="submit"
                    class="px-4 py-2 rounded bg-primary-500 text-white text-lg transition hover:shadow-md"
                >
                    💾 保存する
                </button>
            </div>
        </form>
    </FormWrapper>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import LabeledInput from "@/components/ui/LabeledInput.vue";

const form = ref({
    id: null,
    name: "",
    phone: "",
    address: "",
    map_url: "",
    instagram_url: "",
    line_liff_url: "",
    slot_minutes: 30, // デフォルトは30分
});

const generatedLiffUrl = computed(() => {
    const base = "https://liff.line.me/165XXXXXXXX"; // ← あなたのLIFF IDに置き換えてください
    return form.value.id ? `${base}?shop=${form.value.id}` : "";
});

const copyLiffUrl = () => {
    navigator.clipboard.writeText(generatedLiffUrl.value);
    alert("コピーしました");
};

const submit = async () => {
    try {
        const endpoint = form.value.id
            ? `/api/shop/shops/${form.value.id}`
            : "/api/shop/shops";
        const method = form.value.id ? "put" : "post";
        const { data } = await axios[method](endpoint, form.value);
        form.value.id = data.id ?? form.value.id;
        alert("保存しました");
    } catch (e) {
        console.error("保存エラー", e);
        alert("保存に失敗しました");
    }
};

onMounted(async () => {
    try {
        const { data } = await axios.get("/api/shop/shops");
        if (Array.isArray(data) && data.length > 0) {
            form.value = { ...form.value, ...data[0] };
        }
    } catch (e) {
        console.error("取得エラー", e);
    }
});
</script>
