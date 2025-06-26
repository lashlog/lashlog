<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">メニュー一覧（{{ shopName }}）</h2>

        <div class="mb-4" v-if="shops.length > 1">
            <label>店舗を選択:</label>
            <select v-model="selectedShopId" @change="fetchMenus">
                <option v-for="shop in shops" :key="shop.id" :value="shop.id">
                    {{ shop.name }}
                </option>
            </select>
        </div>

        <table class="table-auto w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">メニュー名</th>
                    <th class="px-4 py-2">カテゴリ</th>
                    <th class="px-4 py-2">価格</th>
                    <th class="px-4 py-2">所要時間</th>
                    <th class="px-4 py-2">公開</th>
                    <th class="px-4 py-2">操作</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="menu in menus" :key="menu.id" class="border-t">
                    <td class="px-4 py-2">{{ menu.name }}</td>
                    <td class="px-4 py-2">{{ menu.category }}</td>
                    <td class="px-4 py-2">
                        ¥{{ menu.price.toLocaleString() }}
                    </td>
                    <td class="px-4 py-2">{{ menu.duration_minutes }}分</td>
                    <td class="px-4 py-2">
                        {{ menu.is_active ? "公開中" : "非公開" }}
                    </td>
                    <td class="px-4 py-2">
                        <button
                            class="text-blue-500"
                            @click="editMenu(menu.id)"
                        >
                            編集
                        </button>
                        |
                        <button
                            class="text-red-500"
                            @click="deleteMenu(menu.id)"
                        >
                            削除
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button
            class="mt-4 px-4 py-2 bg-green-500 text-white"
            @click="goToCreate"
        >
            ＋新規メニュー追加
        </button>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const shops = ref([]);
const selectedShopId = ref(null);
const shopName = ref("");
const menus = ref([]);
const router = useRouter();

const fetchShops = async () => {
    const res = await axios.get("/api/shops");
    shops.value = res.data;

    if (res.data.length > 0) {
        selectedShopId.value = res.data[0].id;
        shopName.value = res.data[0].name;
        fetchMenus();
    }
};

const fetchMenus = async () => {
    if (!selectedShopId.value) return;
    const res = await axios.get(`/api/shops/${selectedShopId.value}/menus`);
    menus.value = res.data;

    const selected = shops.value.find((s) => s.id === selectedShopId.value);
    shopName.value = selected ? selected.name : "";
};

const editMenu = (id) => {
    router.push(`/menus/${id}/edit`);
};

const deleteMenu = async (id) => {
    if (confirm("本当に削除しますか？")) {
        await axios.delete(`/api/menus/${id}`);
        fetchMenus();
    }
};

const goToCreate = () => {
    router.push("/menus/create");
};

onMounted(() => {
    fetchShops();
});
</script>
