<template>
    <div class="p-8 mx-auto mt-12">
        <h2
            class="text-3xl font-bold mb-6 flex items-center gap-2 text-primary-500"
        >
            📋 メニュー一覧
        </h2>

        <div class="mb-6">
            <button
                class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-2xl shadow"
                @click="goToCreate"
            >
                ＋ メニュー追加
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow rounded-lg">
                <thead class="bg-greige-100 text-left">
                    <tr>
                        <th class="px-6 py-3">名前</th>
                        <th class="px-6 py-3">カテゴリ</th>
                        <th class="px-6 py-3">価格</th>
                        <th class="px-6 py-3">所要時間</th>
                        <th class="px-6 py-3">表示</th>
                        <th class="px-6 py-3 text-center">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="menu in menus" :key="menu.id" class="border-b">
                        <td class="px-6 py-4">{{ menu.name }}</td>
                        <td class="px-6 py-4">
                            {{ menu.category?.name ?? "未設定" }}
                        </td>
                        <td class="px-6 py-4">{{ menu.price }}円</td>
                        <td class="px-6 py-4">{{ menu.duration_minutes }}分</td>
                        <td class="px-6 py-4">
                            <span
                                :class="
                                    menu.is_active
                                        ? 'text-green-600'
                                        : 'text-gray-400'
                                "
                            >
                                {{ menu.is_active ? "表示" : "非表示" }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <button
                                class="text-primary-500 font-medium hover:underline"
                                @click="editMenu(menu.id)"
                            >
                                編集
                            </button>
                            <button
                                class="text-red-500 font-medium hover:underline"
                                @click="deleteMenu(menu.id)"
                            >
                                削除
                            </button>
                        </td>
                    </tr>
                    <tr v-if="menus.length === 0">
                        <td class="px-6 py-4 text-center" colspan="6">
                            メニューが登録されていません。
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios"; // 共通のaxiosラッパーを使っている前提

const menus = ref([]);
const router = useRouter();

const fetchMenus = async () => {
    const res = await axios.get("/api/shop/menus");
    menus.value = res.data;
};

const goToCreate = () => {
    router.push("/shop/settings/menus/create");
};

const editMenu = (id) => {
    router.push(`/shop/settings/menus/${id}/edit`);
};

const deleteMenu = async (id) => {
    if (confirm("本当に削除しますか？")) {
        await axios.delete(`/api/shop/menus/${id}`);
        await fetchMenus();
    }
};

onMounted(fetchMenus);
</script>
