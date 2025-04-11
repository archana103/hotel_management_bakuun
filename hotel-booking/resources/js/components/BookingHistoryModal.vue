<template>
    <div v-if="show" class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Booking History</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <div v-if="loading" class="text-center my-3">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
  
            <table v-else class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User</th>
                  <th>Check-In</th>
                  <th>Check-Out</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="booking in bookings" :key="booking.id">
                  <td>{{ booking.id }}</td>
                  <td>{{ booking.user?.name || 'N/A' }}</td>
                  <td>{{ booking.check_in }}</td>
                  <td>{{ booking.check_out }}</td>
                  <td>
                      <select class="form-select form-select-sm" v-model="booking.status" @change="updateStatus(booking)">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                      </select>
                    </td>
                </tr>
                <tr v-if="bookings.length === 0">
                  <td colspan="5" class="text-center text-muted">No bookings found.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue'
  import axios from 'axios'
  
  const props = defineProps(['room'])
  const emit = defineEmits(['close'])
  
  const show = ref(true)
  const bookings = ref([])
  const loading = ref(true)
  
  const closeModal = () => {
    show.value = false
    emit('close')
  }
  
  const fetchHistory = async () => {
    if (!props.room) return
    loading.value = true
    try {
      const token = localStorage.getItem('token')
      const res = await axios.get(`/api/rooms/${props.room.id}/bookings`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      console.log(bookings.value);
      bookings.value = res.data
    } catch (e) {
      console.error(e)
    } finally {
      loading.value = false
    }
  }
  const updateStatus = async (booking) => {
  try {
    const token = localStorage.getItem('token')
    await axios.put(`/api/bookings/${booking.id}`, {
      status: booking.status
    }, {
      headers: { Authorization: `Bearer ${token}` }
    })
    await fetchHistory()
    emit('updated')
  } catch (error) {
    console.error('Failed to update status:', error)
  }
}
  watch(() => props.room, () => {
    if (props.room) {
      show.value = true
      fetchHistory()
    }
  }, { immediate: true })
  </script>
  