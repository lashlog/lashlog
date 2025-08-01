<template>
    <div class="p-8 mx-auto mt-12">
        <h2 class="text-3xl font-bold mb-6 flex items-center gap-2 text-primary-500">
            💰 売上管理
        </h2>

        <div class="mb-4">
            <input type="date" v-model="from" class="border p-2 mr-2" />
            <input type="date" v-model="to" class="border p-2 mr-2" />
            <button class="bg-primary-500 hover:bg-primary-600 text-white py-2 px-4 rounded" @click="fetchSales">
                取得
            </button>
            <button class="ml-2 bg-primary-500 hover:bg-primary-600 text-white py-2 px-4 rounded" @click="openCreate">
                ＋ 売上登録
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow rounded-lg">
                <thead class="bg-greige-100 text-left">
                    <tr>
                        <th class="px-6 py-3">日付</th>
                        <th class="px-6 py-3">金額</th>
                        <th class="px-6 py-3">メモ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="sale in sales" :key="sale.id" class="border-b">
                        <td class="px-6 py-4">{{ sale.sale_date }}</td>
                        <td class="px-6 py-4">{{ sale.amount }}円</td>
                        <td class="px-6 py-4">{{ sale.memo }}</td>
                    </tr>
                    <tr v-if="sales.length === 0">
                        <td class="px-6 py-4 text-center" colspan="3">売上がありません。</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="showForm" class="mt-6">
            <h3 class="text-xl font-bold mb-2">売上登録</h3>
            <div class="space-y-2">
                <input type="date" v-model="form.sale_date" class="border p-2 w-full" />
                <input type="number" v-model="form.amount" class="border p-2 w-full" placeholder="金額" />
                <input type="text" v-model="form.memo" class="border p-2 w-full" placeholder="メモ" />
                <button class="bg-primary-500 hover:bg-primary-600 text-white py-2 px-4 rounded" @click="submit">
                    保存
                </button>
                <button class="ml-2 text-gray-600" @click="showForm = false">キャンセル</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '@/axios'

const sales = ref([])
const from = ref('')
const to = ref('')
const showForm = ref(false)
const form = ref({ sale_date: '', amount: 0, memo: '' })

const fetchSales = async () => {
    const params = {}
    if (from.value && to.value) {
        params.from = from.value
        params.to = to.value
    }
    const res = await axios.get('/api/shop/sales', { params })
    sales.value = res.data
}

const openCreate = () => {
    showForm.value = true
    form.value = { sale_date: '', amount: 0, memo: '' }
}

const submit = async () => {
    await axios.post('/api/shop/sales', form.value)
    showForm.value = false
    await fetchSales()
}

fetchSales()
</script>
