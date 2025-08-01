<template>
  <div class="p-4 max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6 text-center">プラン選択</h1>
    <div class="grid gap-4 md:grid-cols-3">
      <div v-for="p in plans" :key="p.id" class="border rounded-lg p-4 text-center shadow">
        <h2 class="text-xl font-semibold mb-2">{{ p.label }}</h2>
        <p class="text-2xl font-bold mb-4">{{ p.price }}円</p>
        <button @click="subscribe(p.id)" class="w-full bg-primary-500 text-white py-2 rounded">
          申し込む
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from '@/axios'

const plans = [
  { id: 'basic', label: 'ベーシック', price: '9,800' },
  { id: 'pro', label: 'プロ', price: '12,800' },
  { id: 'premium', label: 'プレミアム', price: '18,000' }
]

const subscribe = async (plan) => {
  try {
    await axios.get('/api/sanctum/csrf-cookie')
    const res = await axios.post('/api/stripe/checkout-session', { plan })
    window.location.href = res.data.url
  } catch (e) {
    alert('決済セッションの作成に失敗しました')
  }
}
</script>
