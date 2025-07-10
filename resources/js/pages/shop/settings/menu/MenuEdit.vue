<template>
    <FormWrapper>
        <h2 class="text-3xl font-bold mb-6 text-primary-500">
            ✏️ メニュー編集
        </h2>

        <form @submit.prevent="updateMenu">
            <div class="space-y-4">
                <div>
                    <label class="block font-semibold mb-1">メニュー名</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full border p-2 rounded"
                        required
                    />
                </div>

                <div>
                    <label class="block font-semibold mb-1">カテゴリ</label>
                    <select
                        v-model="form.menu_category_id"
                        class="w-full border p-2 rounded"
                        required
                    >
                        <option value="">選択してください</option>
                        <option
                            v-for="cat in categories"
                            :key="cat.id"
                            :value="cat.id"
                        >
                            {{ cat.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">価格（円）</label>
                    <input
                        v-model="form.price"
                        type="number"
                        class="w-full border p-2 rounded"
                        required
                    />
                </div>

                <div>
                    <label class="block font-semibold mb-1"
                        >所要時間（分）</label
                    >
                    <input
                        v-model="form.duration_minutes"
                        type="number"
                        class="w-full border p-2 rounded"
                        required
                    />
                </div>

                <div>
                    <label class="block font-semibold mb-1"
                        >単位（例：120本）</label
                    >
                    <input
                        v-model="form.unit"
                        type="text"
                        class="w-full border p-2 rounded"
                    />
                </div>

                <div>
                    <label class="block font-semibold mb-1">説明</label>
                    <textarea
                        v-model="form.description"
                        class="w-full border p-2 rounded"
                    ></textarea>
                </div>

                <div>
                    <label class="block font-semibold mb-1"
                        >表示順（ソート）</label
                    >
                    <input
                        v-model="form.sort_order"
                        type="number"
                        class="w-full border p-2 rounded"
                    />
                </div>

                <div class="flex items-center space-x-2">
                    <input
                        type="checkbox"
                        id="is_active"
                        v-model="form.is_active"
                    />
                    <label for="is_active">表示する</label>
                </div>

                <div class="flex gap-4">
                    <button
                        type="submit"
                        class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-6 rounded-2xl shadow"
                    >
                        更新する
                    </button>
                    <button
                        type="button"
                        @click="goBack"
                        class="bg-greige-300 hover:bg-greige-400 text-gray-800 font-semibold py-2 px-4 rounded-2xl shadow"
                    >
                        キャンセル
                    </button>
                </div>
            </div>
        </form>
    </FormWrapper>
</template>

<script setup>
import { ref, onMounted } from "vue";
import FormWrapper from "@/components/FormWrapper.vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();
const id = route.params.id;

const form = ref({
    name: "",
    menu_category_id: "",
    price: 0,
    duration_minutes: 0,
    unit: "",
    description: "",
    sort_order: 0,
    is_active: true,
});

const categories = ref([]);

const fetchMenu = async () => {
    const res = await axios.get(`/api/shop/menus/${id}`);
    form.value = {
        ...res.data,
        is_active: Boolean(res.data.is_active), // ←ここで明示的に true/false に変換
    };
};

const fetchCategories = async () => {
    const res = await axios.get("/api/shop/menu-categories");
    categories.value = res.data;
};

const updateMenu = async () => {
    try {
        await axios.put(`/api/shop/menus/${id}`, form.value);
        alert("メニューを更新しました！");
        router.push("/shop/settings/menus");
    } catch (err) {
        alert("更新に失敗しました");
        console.error(err);
    }
};

const goBack = () => {
    router.push("/shop/settings/menus");
};

onMounted(() => {
    fetchMenu();
    fetchCategories();
});
</script>
