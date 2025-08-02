<template>
    <div>
        <!-- ナビゲーション -->
        <div class="flex items-center justify-between mb-2 text-sm">
            <div class="flex items-center gap-2">
                <button @click="changeMonth(-1)" class="text-primary-500">
                    &lt;前月
                </button>
                <button @click="goToday" class="text-primary-500">今月</button>
                <button @click="changeMonth(1)" class="text-primary-500">
                    翌月&gt;
                </button>
            </div>
            <div class="font-bold text-lg">
                {{ displayMonth }}
            </div>
            <button @click="viewDay" class="text-primary-500">日表示</button>
        </div>

        <!-- カレンダー本体 -->
        <div
            class="grid grid-cols-7 gap-px text-xs"
            :style="{ backgroundColor: 'var(--color-greige-100)' }"
        >
            <div
                v-for="day in days"
                :key="day.date"
                class="bg-white p-1 min-h-24 flex flex-col cursor-pointer"
                :class="{ 'opacity-50': day.isOtherMonth }"
                @click="selectDay(day.date)"
            >
                <div class="flex justify-end">
                    <div
                        class="w-6 h-6 flex items-center justify-center text-sm"
                        :class="[
                            isSelected(day.date)
                                ? 'rounded-full'
                                : '',
                            day.isOtherMonth ? 'text-gray-400' : ''
                        ]"
                        :style="
                            isSelected(day.date)
                                ? { backgroundColor: 'var(--color-primary-100)' }
                                : {}
                        "
                    >
                        {{ formatDayNumber(day.date) }}
                    </div>
                </div>
                <div class="mt-1 space-y-0.5">
                    <div
                        v-for="res in day.reservations.slice(0, 3)"
                        :key="res.id"
                        class="truncate text-primary-500 cursor-pointer"
                        @click.stop="openDetail(res.id)"
                    >
                        {{ res.customer_name }}（{{ res.menu_name }}）
                    </div>
                    <div
                        v-if="day.reservations.length > 3"
                        class="text-right text-primary-500 cursor-pointer"
                        @click.stop="openList(day.date)"
                    >
                        他{{ day.reservations.length - 3 }}件
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue'
import dayjs from 'dayjs'

const props = defineProps({
    reservationsByDate: Array,
    currentMonth: String,
})

const emit = defineEmits([
    'select-date',
    'view-day',
    'open-reservation-detail',
    'open-reservation-list',
    'update:currentMonth',
])

const selectedDate = ref(dayjs().format('YYYY-MM-DD'))

const month = ref(dayjs(`${props.currentMonth}-01`))

watch(
    () => props.currentMonth,
    (val) => {
        month.value = dayjs(`${val}-01`)
    }
)

onMounted(() => {
    emit('update:currentMonth', month.value.format('YYYY-MM'))
})

const reservationsMap = computed(() => {
    const map = {}
    for (const item of props.reservationsByDate) {
        map[item.date] = item.reservations
    }
    return map
})

const days = computed(() => {
    const start = month.value.startOf('month').startOf('week')
    const end = month.value.endOf('month').endOf('week')
    const arr = []
    for (let d = start.clone(); d.isSame(end) || d.isBefore(end); d = d.add(1, 'day')) {
        const dateStr = d.format('YYYY-MM-DD')
        arr.push({
            date: dateStr,
            reservations: reservationsMap.value[dateStr] || [],
            isOtherMonth: d.month() !== month.value.month(),
        })
    }
    return arr
})

const displayMonth = computed(() => month.value.format('YYYY年M月'))

const formatDayNumber = (dateStr) => dayjs(dateStr).date()

const isSelected = (dateStr) => selectedDate.value === dateStr

const selectDay = (dateStr) => {
    selectedDate.value = dateStr
    emit('select-date', dateStr)
}

const viewDay = () => {
    emit('view-day', selectedDate.value)
}

const changeMonth = (diff) => {
    month.value = month.value.add(diff, 'month')
    emit('update:currentMonth', month.value.format('YYYY-MM'))
}

const goToday = () => {
    month.value = dayjs().startOf('month')
    emit('update:currentMonth', month.value.format('YYYY-MM'))
}

const openDetail = (id) => emit('open-reservation-detail', id)

const openList = (dateStr) => emit('open-reservation-list', dateStr)
</script>

<style scoped>
.min-h-24 {
    min-height: 6rem;
}
</style>
