<template>
  <div class="modal fade show d-block" tabindex="-1" v-if="show" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Header -->
        <div class="modal-header">
          <h5 class="modal-title">Book Room</h5>
          <button type="button" class="btn-close" @click="onClose"></button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <form @submit.prevent="submitBooking">
            <!-- User Dropdown -->
            <div class="mb-3">
              <label class="form-label">Select User</label>
              <select v-model="selectedUserId" class="form-select" required>
                <option value="" disabled>Select a user</option>
                <option v-for="user in users" :key="user.id" :value="user.id">
                  {{ user.name }} ({{ user.email }})
                </option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Check-in Date</label>
              <input type="date" v-model="form.check_in" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Check-out Date</label>
              <input type="date" v-model="form.check_out" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Guests</label>
              <input type="number" v-model="form.guests" class="form-control" min="1" required />
            </div>

            <div v-if="error" class="text-danger mb-2">{{ error }}</div>
            <button type="submit" class="btn btn-primary w-100">Confirm Booking</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

// Props and Emits
const props = defineProps({
  roomId: Number,
  show: Boolean
})
const emit = defineEmits(['close', 'booked'])

// Reactive States
const form = ref({
  check_in: '',
  check_out: '',
  guests: 1
})
const users = ref([])
const selectedUserId = ref('')
const error = ref('')

// Reset modal state
const onClose = () => {
  form.value = { check_in: '', check_out: '', guests: 1 }
  selectedUserId.value = ''
  error.value = ''
  emit('close')
}

// Fetch users with 'user' role
const fetchUsers = async () => {
  try {
    const token = localStorage.getItem('token')
    if (!token) throw new Error('Auth token missing')

    const { data } = await axios.get('/api/users?role=users', {
      headers: { Authorization: `Bearer ${token}` }
    })

    users.value = data
  } catch (err) {
    console.error('Failed to fetch users:', err)
    users.value = []
  }
}

// Watch for modal visibility to trigger user fetch
watch(() => props.show, (val) => {

  if (val) fetchUsers()
}, { immediate: true })


// Submit booking request
const submitBooking = async () => {
  error.value = ''

  if (!selectedUserId.value) {
    error.value = 'Please select a user.'
    return
  }

  if (new Date(form.value.check_in) >= new Date(form.value.check_out)) {
    error.value = 'Check-out date must be after check-in date.'
    return
  }

  try {
    const token = localStorage.getItem('token')
    if (!token) throw new Error('Auth token missing')

    await axios.post(
      '/api/bookings',
      {
        room_id: props.roomId,
        user_id: selectedUserId.value,
        check_in: form.value.check_in,
        check_out: form.value.check_out,
        guests: form.value.guests
      },
      {
        headers: { Authorization: `Bearer ${token}` }
      }
    )

    alert('Booking successful!')
    emit('booked')
    onClose()
  } catch (err) {
    console.error('Booking error:', err)
    error.value = err.response?.data?.message || 'Booking failed. Try again.'
  }
}
</script>
