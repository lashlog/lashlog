<template>
    <FormWrapper>
        <h2 class="text-3xl font-bold mb-6 text-primary-500">メニュー作成</h2>

        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block font-medium">メニュー名</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="border rounded w-full p-2"
                />
            </div>

            <div class="mb-4">
                <label class="block font-medium">カテゴリー</label>
                <select
                    v-model="form.menu_category_id"
                    class="border rounded w-full p-2"
                    @change="handleCategoryChange"
                >
                    <option disabled value="">選択してください</option>
                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                    >
                        {{ category.name }}
                    </option>
                </select>
            </div>

            <div v-if="selectedCategoryType === 'extension'" class="mb-4">
                <label class="block font-medium">単位（例：120本）</label>
                <input
                    v-model="form.unit"
                    type="text"
                    class="border rounded w-full p-2"
                />
            </div>

            <div class="mb-4">
                <label class="block font-medium">料金（円）</label>
                <input
                    v-model.number="form.price"
                    type="number"
                    class="border rounded w-full p-2"
                />
            </div>

            <div class="mb-4">
                <label class="block font-medium">所要時間（分）</label>
                <input
                    v-model.number="form.duration_minutes"
                    type="number"
                    class="border rounded w-full p-2"
                />
            </div>
            <div class="mb-4">
                <label class="block font-medium">表示順（ソート順）</label>
                <input
                    v-model.number="form.sort_order"
                    type="number"
                    class="border rounded w-full p-2"
                />
            </div>
            <div class="mb-4">
                <label class="block font-medium">説明</label>
                <textarea
                    v-model="form.description"
                    class="border rounded w-full p-2"
                ></textarea>
            </div>

            <div class="mb-8">
                <label>
                    <input
                        v-model="form.is_active"
                        type="checkbox"
                        class="mr-2"
                    />
                    表示する
                </label>
            </div>
            <div class="flex gap-4">
                <button
                    type="submit"
                    class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-6 rounded-2xl shadow"
                >
                    登録する
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
import axios from "axios";
import FormWrapper from "@/components/FormWrapper.vue";
import { ref, onMounted, watch } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const form = ref({
    name: "",
    menu_category_id: "",
    unit: "",
    price: null,
    duration_minutes: null,
    sort_order: null,
    description: "",
    is_active: true,
});

const categories = ref([]);
const selectedCategoryType = ref("");

onMounted(async () => {
    try {
        const res = await axios.get("/api/shop/menu-categories");
        categories.value = res.data;
    } catch (e) {
        console.error("カテゴリ取得失敗", e);
    }
});
function handleCategoryChange() {
    const cat = categories.value.find(
        (c) => c.id === form.value.menu_category_id
    );
    selectedCategoryType.value = cat?.type ?? "";
    if (selectedCategoryType.value !== "extension") {
        form.value.unit = "";
    }
}
const submit = async () => {
    try {
        await axios.post("/api/shop/menus", form.value);
        alert("メニューを作成しました");
        router.push("/shop/settings/menus");
    } catch (e) {
        console.error(e);
        alert("登録に失敗しました");
    }
};
const goBack = () => {
    router.push("/shop/settings/menus");
};
</script>
