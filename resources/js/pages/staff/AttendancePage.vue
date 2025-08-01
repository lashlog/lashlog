<template>
  <div class="p-4 space-y-4">
    <h1>出退勤打刻</h1>
    <button @click="start" class="btn">出勤</button>
    <button @click="end" class="btn" v-if="records.length">退勤</button>
    <ul>
      <li v-for="rec in records" :key="rec.id">
        {{ rec.started_at }} - {{ rec.ended_at || '勤務中' }}
      </li>
    </ul>
  </div>
</template>
<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'
const records = ref([])
const start = async () => {
  const res = await axios.post('/api/staff/attendance/start')
  records.value.push(res.data)
}
const end = async () => {
  const last = records.value[records.value.length - 1]
  const res = await axios.post(`/api/staff/attendance/${last.id}/end`)
  Object.assign(last, res.data)
}
const load = async () => {
  const res = await axios.get('/api/staff/attendances')
  records.value = res.data
}
onMounted(load)
</script>
