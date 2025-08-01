<template>
    <div class="grid grid-cols-7 gap-px bg-gray-200 text-xs">
        <div
            v-for="day in days"
            :key="day.date.format('YYYY-MM-DD')"
            class="bg-white h-28 p-1 overflow-hidden cursor-pointer"
            :class="{
                'bg-primary-100': isSelected(day.date),
                'opacity-50': day.isOtherMonth,
            }"
            @click="selectDay(day.date)"
            @dblclick="createReservation(day.date)"
        >
            <div
                class="font-bold text-sm"
                :class="day.isOtherMonth ? 'text-gray-400' : ''"
            >
                {{ day.date.date() }}
            </div>
            <div
                v-for="res in day.reservations"
                :key="res.id"
                class="mt-1 truncate"
            >
                {{ res.start_time }} {{ staffName(res.staff_id) }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import dayjs from 'dayjs'

const props = defineProps({
    reservations: Array,
    staffList: Array,
    currentDate: String,
})
const emit = defineEmits(['update:date', 'create-reservation'])

const staffName = (id) => {
    const s = props.staffList.find((st) => st.id === id)
    return s ? s.name : ''
}

const isSelected = (date) => {
    return date.format('YYYY-MM-DD') === props.currentDate
}

const selectDay = (date) => {
    emit('update:date', date.format('YYYY-MM-DD'))
}

const createReservation = (date) => {
    const staffId = props.staffList[0]?.id || null
    emit('create-reservation', {
        reserved_date: date.format('YYYY-MM-DD'),
        start_time: '09:00',
        staff_id: staffId,
    })
}

const days = computed(() => {
    const start = dayjs(props.currentDate).startOf('month').startOf('week')
    const end = dayjs(props.currentDate).endOf('month').endOf('week')
    const arr = []
    for (let d = start; d.isBefore(end) || d.isSame(end, 'day'); d = d.add(1, 'day')) {
        const list = props.reservations.filter(
            (r) => r.reserved_date === d.format('YYYY-MM-DD')
        )
        arr.push({
            date: d,
            reservations: list,
            isOtherMonth: d.month() !== dayjs(props.currentDate).month(),
        })
    }
    return arr
})
</script>

<style scoped>
.grid > div {
    min-height: 7rem;
}
</style>
