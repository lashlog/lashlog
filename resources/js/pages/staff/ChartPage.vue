<template>
  <div class="p-4">
    <h1>カルテ登録</h1>
    <form @submit.prevent="save">
      <input v-model="note" class="border" />
      <button class="btn">登録</button>
    </form>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import axios from 'axios'
const note = ref('')
const save = async () => {
  await axios.post('/api/staff/charts', { note: note.value })
  note.value = ''
}
</script>
