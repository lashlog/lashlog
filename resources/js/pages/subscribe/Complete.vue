<template>
  <div class="p-4 text-center">
    <p v-if="loading">確認中...</p>
    <p v-else-if="error" class="text-red-500">{{ error }}</p>
    <p v-else>決済が完了しました。アカウント登録へ進みます。</p>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from '@/axios'

const route = useRoute()
const router = useRouter()
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  const sessionId = route.query.session_id
  if (!sessionId) {
    error.value = 'セッションIDがありません'
    loading.value = false
    return
  }
  try {
    const res = await axios.get(`/api/stripe/session/${sessionId}`)
    if (res.data.paid) {
      router.push('/register')
    } else {
      error.value = '支払いが確認できませんでした'
    }
  } catch (e) {
    error.value = '確認に失敗しました'
  } finally {
    loading.value = false
  }
})
</script>
