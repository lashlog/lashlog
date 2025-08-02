<template>
    <div class="p-8 mx-auto mt-12 max-w-xl">
        <h2 class="text-3xl font-bold mb-6 text-primary-500">割引ルール編集</h2>
        <div class="space-y-4" v-if="loaded">
            <div>
                <label class="block mb-1 text-sm font-medium">名前</label>
                <input v-model="form.name" class="w-full p-2 border rounded" />
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium">条件タイプ</label>
                <select v-model="form.condition_type" class="w-full p-2 border rounded">
                    <option value="first_time">初回</option>
                    <option value="repeat_within_days">リピート日数</option>
                    <option value="birthday_month">誕生日月</option>
                    <option value="reservation_source">予約媒体</option>
                    <option value="coupon">クーポンコード</option>
                </select>
            </div>
            <div v-if="needsConditionValue">
                <label class="block mb-1 text-sm font-medium">条件値</label>
                <input v-model="form.condition_value" class="w-full p-2 border rounded" />
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium">割引タイプ</label>
                <select v-model="form.discount_type" class="w-full p-2 border rounded">
                    <option value="fixed">金額</option>
                    <option value="percent">割合</option>
                </select>
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium">割引値</label>
                <input type="number" v-model.number="form.discount_value" class="w-full p-2 border rounded" />
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium">対象メニュー</label>
                <select v-model="form.target_menu_ids" multiple class="w-full p-2 border rounded h-32">
                    <option v-for="menu in menus" :key="menu.id" :value="menu.id">{{ menu.name }}</option>
                </select>
            </div>
            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" v-model="form.is_active" class="mr-2" />有効
                </label>
            </div>
            <div class="flex justify-end space-x-2">
                <button class="px-4 py-2 bg-gray-300 rounded" @click="goBack">戻る</button>
                <button class="px-4 py-2 bg-primary-500 text-white rounded" @click="save">保存</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const menus = ref([]);
const loaded = ref(false);
const form = ref({
    name: '',
    condition_type: 'first_time',
    condition_value: '',
    discount_type: 'fixed',
    discount_value: 0,
    target_menu_ids: [],
    is_active: true,
});
const needsConditionValue = computed(() => ['repeat_within_days', 'reservation_source', 'coupon'].includes(form.value.condition_type));
const fetchMenus = async () => {
    const res = await axios.get('/api/shop/menus');
    menus.value = res.data;
};
const fetchRule = async () => {
    const res = await axios.get(`/api/shop/discount-rules/${route.params.id}`);
    Object.assign(form.value, res.data);
    loaded.value = true;
};
const save = async () => {
    await axios.put(`/api/shop/discount-rules/${route.params.id}`, form.value);
    router.push('/shop/settings/discount-rules');
};
const goBack = () => router.push('/shop/settings/discount-rules');
onMounted(async () => {
    await fetchMenus();
    await fetchRule();
});
</script>
