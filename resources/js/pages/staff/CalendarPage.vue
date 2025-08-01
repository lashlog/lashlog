<template>
  <div class="p-4">
    <h1>予約カレンダー</h1>
    <vue-cal :events="events" style="height: 600px" />
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import VueCal from 'vue-cal'
import 'vue-cal/dist/vuecal.css'
import axios from 'axios'
const events = ref([])
const load = async () => {
  const res = await axios.get('/api/shop/reservations')
  events.value = res.data.map(r => ({
    start: r.reserved_date + ' ' + r.start_time,
    end: r.reserved_date + ' ' + r.end_time,
    title: r.customer ? r.customer.name : '予約'
  }))
}
onMounted(load)
</script>
