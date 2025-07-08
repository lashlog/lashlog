<template>
    <div class="p-8 mx-auto mt-12">
        <h2
            class="text-3xl font-bold mb-6 flex items-center gap-2 text-primary-500"
        >
            👩‍💼 スタッフ一覧
        </h2>

        <div class="mb-6">
            <button
                class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-2xl shadow"
                @click="goToCreate"
            >
                ＋ スタッフ追加
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow rounded-lg">
                <thead class="bg-greige-100 text-left">
                    <tr>
                        <th class="px-6 py-3">名前</th>
                        <th class="px-6 py-3">メールアドレス</th>
                        <th class="px-6 py-3">権限</th>
                        <th class="px-6 py-3 text-center">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="staff in staffs"
                        :key="staff.id"
                        class="border-b"
                    >
                        <td class="px-6 py-4">{{ staff.name }}</td>
                        <td class="px-6 py-4">{{ staff.email }}</td>
                        <td class="px-6 py-4">
                            {{
                                staff.role === "owner" ? "オーナー" : "スタッフ"
                            }}
                        </td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <button
                                class="text-primary-500 font-medium hover:underline"
                                @click="editStaff(staff.id)"
                            >
                                編集
                            </button>
                            <button
                                class="text-red-500 font-medium hover:underline"
                                @click="deleteStaff(staff.id)"
                            >
                                削除
                            </button>
                        </td>
                    </tr>
                    <tr v-if="staffs.length === 0">
                        <td class="px-6 py-4 text-center" colspan="4">
                            スタッフが登録されていません。
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
import axios from "axios";

const router = useRouter();
const staffs = ref([]);

const fetchStaffs = async () => {
    const res = await axios.get("/api/shop/staffs");
    staffs.value = res.data;
};

const goToCreate = () => {
    router.push("/staffs/create");
};

const editStaff = (id) => {
    router.push(`/staffs/${id}/edit`);
};

const deleteStaff = async (id) => {
    if (confirm("本当に削除しますか？")) {
        await axios.delete(`/api/staffs/${id}`);
        await fetchStaffs();
    }
};

onMounted(fetchStaffs);
</script>
