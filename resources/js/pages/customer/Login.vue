<template>
  <div class="p-4 max-w-sm mx-auto">
    <h1 class="text-xl font-bold mb-4">ログイン</h1>
    <form @submit.prevent="handleLogin" class="space-y-3">
      <div>
        <label class="block text-sm mb-1" for="email">メールアドレス</label>
        <input id="email" v-model="email" type="email" required class="w-full border px-2 py-1" />
      </div>
      <div>
        <label class="block text-sm mb-1" for="password">パスワード</label>
        <input id="password" v-model="password" type="password" required class="w-full border px-2 py-1" />
      </div>
      <button type="submit" class="w-full bg-primary-500 text-white py-2 rounded">ログイン</button>
    </form>
    <p class="text-red-500 mt-2" v-if="error">{{ error }}</p>
    <button @click="handleLineLogin" class="mt-4 w-full bg-green-500 text-white py-2 rounded">LINEでログイン</button>
    <p class="mt-4 text-center">
      <router-link to="/customer/register" class="text-blue-500 underline">会員登録はこちら</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '@/axios'
import router from '@/router'

const email = ref('')
const password = ref('')
const error = ref('')

const handleLogin = async () => {
  error.value = ''
  try {
    await axios.get('/api/sanctum/csrf-cookie')
    const res = await axios.post('/api/customer/login', { email: email.value, password: password.value })
    console.log(res.data)
    router.push('/customer/users')
  } catch (e) {
    error.value = 'ログインに失敗しました'
  }
}

const handleLineLogin = async () => {
  // LIFFから取得したlineIdを使用する想定
  const lineId = window.liffId || ''
  if (!lineId) {
    error.value = 'LINE IDを取得できませんでした'
    return
  }
  try {
    await axios.get('/api/sanctum/csrf-cookie')
    const res = await axios.post('/api/customer/line-login', { line_id: lineId })
    console.log(res.data)
    router.push('/customer/users')
  } catch (e) {
    // 登録されていなければ会員登録へ
    router.push('/customer/register')
  }
}
</script>
