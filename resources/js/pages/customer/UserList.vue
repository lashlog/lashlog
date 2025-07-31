<template>
  <div class="p-4">
    <h1 class="text-xl font-bold mb-2">Users</h1>
    <ul>
      <li v-for="user in users" :key="user.id" class="border-b py-1">
        {{ user.name }} ({{ user.email }})
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '@/axios'

const users = ref([])

onMounted(async () => {
  const response = await axios.get('/api/users')
  users.value = response.data
})
</script>
