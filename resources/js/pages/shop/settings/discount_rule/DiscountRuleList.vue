<template>
    <div class="p-8 mx-auto mt-12">
        <h2 class="text-3xl font-bold mb-6 flex items-center gap-2 text-primary-500">
            🎟 割引ルール一覧
        </h2>
        <div class="mb-6">
            <button class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-2xl shadow" @click="goToCreate">
                ＋ ルール追加
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow rounded-lg">
                <thead class="bg-greige-100 text-left">
                    <tr>
                        <th class="px-6 py-3">名前</th>
                        <th class="px-6 py-3">条件</th>
                        <th class="px-6 py-3">割引</th>
                        <th class="px-6 py-3">有効</th>
                        <th class="px-6 py-3 text-center">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="rule in rules" :key="rule.id" class="border-b">
                        <td class="px-6 py-4">{{ rule.name }}</td>
                        <td class="px-6 py-4">{{ rule.condition_type }}</td>
                        <td class="px-6 py-4">
                            {{ rule.discount_type === 'percent' ? rule.discount_value + '%OFF' : rule.discount_value + '円OFF' }}
                        </td>
                        <td class="px-6 py-4">
                            <span :class="rule.is_active ? 'text-green-600' : 'text-gray-400'">
                                {{ rule.is_active ? '有効' : '無効' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <button class="text-primary-500 font-medium hover:underline" @click="editRule(rule.id)">編集</button>
                            <button class="text-red-500 font-medium hover:underline" @click="deleteRule(rule.id)">削除</button>
                        </td>
                    </tr>
                    <tr v-if="rules.length === 0">
                        <td class="px-6 py-4 text-center" colspan="5">ルールが登録されていません。</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const rules = ref([]);
const router = useRouter();

const fetchRules = async () => {
    const res = await axios.get('/api/shop/discount-rules');
    rules.value = res.data;
};
const goToCreate = () => {
    router.push('/shop/settings/discount-rules/create');
};
const editRule = (id) => {
    router.push(`/shop/settings/discount-rules/${id}/edit`);
};
const deleteRule = async (id) => {
    if (confirm('本当に削除しますか？')) {
        await axios.delete(`/api/shop/discount-rules/${id}`);
        await fetchRules();
    }
};
onMounted(fetchRules);
</script>
