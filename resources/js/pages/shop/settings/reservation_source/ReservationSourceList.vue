<template>
    <div class="p-4">
        <h2
            class="text-3xl font-bold mb-6 flex items-center gap-2 text-primary-500"
        >
            予約元媒体一覧
        </h2>
        <div class="mb-6">
            <button
                class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-2xl shadow"
                @click="goToCreate"
            >
                ＋ 予約元追加
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow rounded-lg">
                <thead class="bg-greige-100 text-left">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">媒体名</th>
                        <th class="px-6 py-3">有効</th>
                        <th class="px-6 py-3">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="source in sources"
                        :key="source.id"
                        class="border-b"
                    >
                        <td class="px-6 py-4">{{ source.id }}</td>
                        <td class="px-6 py-4">{{ source.name }}</td>
                        <td class="px-6 py-4">
                            {{ source.is_active ? "○" : "×" }}
                        </td>
                        <td class="px-6 py-4">
                            <template v-if="!source.system_reserved">
                                <RouterLink
                                    :to="{
                                        name: 'shop.settings.reservation_sources.edit',
                                        params: { id: source.id },
                                    }"
                                    class="text-primary-600 underline mr-2"
                                >
                                    編集
                                </RouterLink>
                                <button
                                    @click="deleteSource(source.id)"
                                    class="text-red-500 underline"
                                >
                                    削除
                                </button>
                            </template>
                            <template v-else>
                                <span class="text-gray-400">（編集不可）</span>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="sources.length === 0" class="p-4 text-center">
                予約元が登録されていません。
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();
const sources = ref([]);

const fetchSources = async () => {
    const res = await axios.get("/api/shop/reservation-sources");
    sources.value = res.data;
};

const goToCreate = () => {
    router.push({ name: "shop.settings.reservation_sources.create" });
};

const editSource = (id) => {
    router.push({
        name: "shop.settings.reservation_sources.edit",
        params: { id },
    });
};

const deleteSource = async (id) => {
    if (!confirm("本当に削除しますか？")) return;
    await axios.delete(`/api/reservation-sources/${id}`);
    fetchSources();
};

onMounted(fetchSources);
</script>
