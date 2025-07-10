<template>
    <FormWrapper>
        <div>
            <h2
                class="text-3xl font-bold mb-6 flex items-center gap-2 text-primary-500"
            >
                ✏️ スタッフ編集
            </h2>

            <form @submit.prevent="updateStaff" class="space-y-6 max-w-xl">
                <div>
                    <label class="block mb-1 font-semibold">名前</label>
                    <input
                        type="text"
                        v-model="form.name"
                        class="w-full border border-gray-300 rounded px-4 py-2"
                        required
                    />
                </div>

                <div>
                    <label class="block mb-1 font-semibold"
                        >メールアドレス</label
                    >
                    <input
                        type="email"
                        v-model="form.email"
                        class="w-full border border-gray-300 rounded px-4 py-2"
                        required
                    />
                </div>

                <div>
                    <label class="block mb-1 font-semibold">権限</label>
                    <select
                        v-model="form.role"
                        class="w-full border border-gray-300 rounded px-4 py-2"
                    >
                        <option value="owner">オーナー</option>
                        <option value="staff">スタッフ</option>
                    </select>
                </div>

                <div class="flex gap-4 mt-6">
                    <button
                        type="submit"
                        class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-2xl shadow"
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
            </form>
        </div>
    </FormWrapper>
</template>

<script setup>
import { ref, onMounted } from "vue";
import FormWrapper from "@/components/FormWrapper.vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios"; // もしくは "@/plugins/axios"

const route = useRoute();
const router = useRouter();
const staffId = route.params.id;

const form = ref({
    name: "",
    email: "",
    role: "staff",
});

const fetchStaff = async () => {
    const res = await axios.get(`/api/shop/staffs/${staffId}`);
    form.value = {
        name: res.data.name,
        email: res.data.email,
        role: res.data.role,
    };
};

const updateStaff = async () => {
    await axios.put(`/api/shop/staffs/${staffId}`, form.value);
    router.push("/shop/settings/staffs");
};

const goBack = () => {
    router.push("/shop/settings/staffs");
};

onMounted(fetchStaff);
</script>
