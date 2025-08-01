<template>
  <div class="p-4">
    <h1>シフト提出</h1>
    <form @submit.prevent="submit" class="space-y-2">
      <input type="date" v-model="date" class="border" />
      <div v-if="isPartTime" class="space-x-1">
        <input type="time" v-model="startTime" class="border" />
        <input type="time" v-model="endTime" class="border" />
      </div>
      <label>
        <input type="checkbox" v-model="paid" /> 有給
      </label>
      <button class="btn">送信</button>
    </form>
    <ul>
      <li v-for="s in shifts" :key="s.id">
        {{ s.date }} {{ s.start_time || '' }}{{ s.start_time ? '-' : ''}}{{ s.end_time || '' }} {{ s.paid_leave ? '有給' : '' }}
      </li>
    </ul>
  </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useStaffStore } from '@/stores/staff'
const staffStore = useStaffStore()
const isPartTime = computed(() => staffStore.staff?.employment_type === 'parttime')
const date = ref('')
const startTime = ref('')
const endTime = ref('')
const paid = ref(false)
const shifts = ref([])
const load = async () => {
  const res = await axios.get('/api/staff/shifts')
  shifts.value = res.data
}
const submit = async () => {
  await axios.post('/api/staff/shifts', {
    date: date.value,
    start_time: startTime.value || null,
    end_time: endTime.value || null,
    paid_leave: paid.value
  })
  await load()
}
onMounted(load)
</script>
