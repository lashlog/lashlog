<template>
  <div class="p-4 max-w-sm mx-auto">
    <h1 class="text-xl font-bold mb-4">会員登録</h1>
    <form @submit.prevent="handleRegister" class="space-y-3">
      <div>
        <label class="block text-sm mb-1" for="name">名前</label>
        <input id="name" v-model="name" required class="w-full border px-2 py-1" />
      </div>
      <div>
        <label class="block text-sm mb-1" for="birthday">生年月日</label>
        <input id="birthday" v-model="birthday" type="date" class="w-full border px-2 py-1" />
      </div>
      <div>
        <label class="block text-sm mb-1" for="phone">電話番号</label>
        <input id="phone" v-model="phone" class="w-full border px-2 py-1" />
      </div>
      <div>
        <label class="block text-sm mb-1" for="email">メールアドレス</label>
        <input id="email" v-model="email" type="email" required class="w-full border px-2 py-1" />
      </div>
      <div>
        <label class="block text-sm mb-1" for="password">パスワード</label>
        <input id="password" v-model="password" type="password" required class="w-full border px-2 py-1" />
      </div>
      <div>
        <label class="block text-sm mb-1" for="password_confirmation">パスワード確認</label>
        <input id="password_confirmation" v-model="passwordConfirmation" type="password" required class="w-full border px-2 py-1" />
      </div>
      <button type="submit" class="w-full bg-primary-500 text-white py-2 rounded">登録</button>
    </form>
    <p class="text-red-500 mt-2" v-if="error">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '@/axios'
import router from '@/router'

const name = ref('')
const birthday = ref('')
const phone = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const error = ref('')

const handleRegister = async () => {
  error.value = ''
  const payload = {
    name: name.value,
    birthday: birthday.value || null,
    phone: phone.value || null,
    email: email.value,
    password: password.value,
    password_confirmation: passwordConfirmation.value,
    line_id: window.liffId || null,
  }
  try {
    await axios.get('/api/sanctum/csrf-cookie')
    const res = await axios.post('/api/customer/register', payload)
    console.log(res.data)
    router.push('/customer/users')
  } catch (e) {
    error.value = '登録に失敗しました'
  }
}
</script>
