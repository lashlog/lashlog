import { defineStore } from 'pinia'

export const useStaffStore = defineStore('staff', {
  state: () => ({
    staff: null
  }),
  actions: {
    setStaff(data) {
      this.staff = data
    },
    clearStaff() {
      this.staff = null
    }
  }
})
