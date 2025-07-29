<template>
    <div class="p-8 mx-auto">
        <h2
            class="text-3xl font-bold mb-6 flex items-center gap-2 text-primary-500"
        >
            👥 顧客一覧
        </h2>
        <div class="mb-6">
            <RouterLink
                :to="{ name: 'shop.customer.create' }"
                class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-2xl shadow"
            >
                ＋ 新規登録
            </RouterLink>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 rounded">
                <thead class="bg-greige-100 text-left">
                    <tr>
                        <th class="px-4 py-2 border-b">名前</th>
                        <th class="px-4 py-2 border-b">電話番号</th>
                        <th class="px-4 py-2 border-b">メールアドレス</th>
                        <th class="px-4 py-2 border-b">登録日</th>
                        <th class="px-4 py-2 border-b">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="customer in customers"
                        :key="customer.id"
                        class="border-b hover:bg-greige-50"
                    >
                        <td class="px-4 py-2">{{ customer.name }}</td>
                        <td class="px-4 py-2">{{ customer.phone || "-" }}</td>
                        <td class="px-4 py-2">{{ customer.email || "-" }}</td>
                        <td class="px-4 py-2">
                            {{ formatDate(customer.created_at) }}
                        </td>
                        <td class="px-4 py-2">
                            <RouterLink
                                :to="{
                                    name: 'shop.customer.edit',
                                    params: { id: customer.id },
                                }"
                                class="text-primary-500 underline"
                            >
                                編集
                            </RouterLink>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();
const customers = ref([]);

const fetchCustomers = async () => {
    const res = await axios.get("/api/shop/customers");
    customers.value = res.data;
};

const formatDate = (dateStr) => {
    const d = new Date(dateStr);
    return `${d.getFullYear()}/${d.getMonth() + 1}/${d.getDate()}`;
};

onMounted(fetchCustomers);
</script>
