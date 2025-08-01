<template>
  <div class="min-h-screen flex items-center justify-center bg-greige-50">
    <div class="w-full max-w-sm bg-white p-8 shadow-xl rounded-2xl">
      <h2 class="text-2xl font-bold text-center text-greige-700 mb-6">スタッフログイン</h2>
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label for="email" class="block text-sm text-greige-700 mb-1">メールアドレス</label>
          <input v-model="email" id="email" type="email" required class="w-full px-4 py-2 border border-gray-300 rounded-md" />
        </div>
        <div>
          <label for="password" class="block text-sm text-greige-700 mb-1">パスワード</label>
          <input v-model="password" id="password" type="password" required class="w-full px-4 py-2 border border-gray-300 rounded-md" />
        </div>
        <button type="submit" class="w-full bg-primary-500 text-white py-2 rounded-md">ログイン</button>
        <p v-if="error" class="text-red-500 text-sm text-center">{{ error }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useStaffStore } from '@/stores/staff'

const router = useRouter()
const staffStore = useStaffStore()
const email = ref('')
const password = ref('')
const error = ref('')

onMounted(async () => {
  try {
    const res = await axios.get('/api/staff/me', { withCredentials: true })
    staffStore.setStaff(res.data)
    router.push('/staff/attendance')
  } catch {}
})

const handleLogin = async () => {
  error.value = ''
  try {
    await axios.get('/api/sanctum/csrf-cookie', { withCredentials: true })
    await axios.post('/api/staff/login', { email: email.value, password: password.value }, { withCredentials: true })
    const res = await axios.get('/api/staff/me', { withCredentials: true })
    staffStore.setStaff(res.data)
    router.push('/staff/attendance')
  } catch (e) {
    error.value = 'ログインに失敗しました'
  }
}
</script>
