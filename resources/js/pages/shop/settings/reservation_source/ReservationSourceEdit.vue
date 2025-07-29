<template>
    <div class="p-4">
        <h2 class="text-xl font-bold mb-4">予約元媒体編集</h2>

        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block mb-1 text-sm font-semibold">表示名</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="w-full p-2 border rounded"
                    :class="{ 'border-red-500': errors.name }"
                />
                <p v-if="errors.name" class="text-red-500 text-sm mt-1">
                    {{ errors.name }}
                </p>
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

            <div class="flex space-x-4">
                <button
                    type="submit"
                    class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-2xl shadow"
                >
                    更新する
                </button>
                <router-link
                    to="/shop/settings/reservation-sources"
                    class="bg-greige-300 hover:bg-greige-400 text-gray-800 font-semibold py-2 px-4 rounded-2xl shadow"
                >
                    キャンセル
                </router-link>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();
const form = ref({
    name: "",
    is_active: true,
});
const errors = ref({});

const load = async () => {
    try {
        const res = await axios.get(
            `/api/shop/reservation-sources/${route.params.id}`
        );
        form.value = {
            name: res.data.name,
            is_active: Boolean(res.data.is_active),
        };
        console.log(form.value.is_active);
    } catch (e) {
        console.error(e);
        alert("取得に失敗しました");
    }
};

const submit = async () => {
    try {
        await axios.put(
            `/api/shop/reservation-sources/${route.params.id}`,
            form.value
        );
        router.push("/shop/settings/reservation-sources");
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors;
        } else {
            console.error(e);
            alert("更新に失敗しました");
        }
    }
};

onMounted(load);
</script>
