<template>
  <div class="container py-5">
    <h2 class="mb-4 fw-bold textcolor"> <i class="bi bi-calendar-check me-2"></i> My Bookings</h2>

    <div class="row g-4" v-if="bookings.length">
      <div class="col-12 col-md-6 col-lg-6" v-for="booking in bookings" :key="booking.id">
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
          <div class="row g-0 h-100">
            <!-- Image -->
            <div class="col-5">
              <img :src="getImageUrl(booking.room.hotel.image)" class="img-fluid h-100 w-100 object-fit-cover"
                style="min-height: 200px;" alt="Hotel Image" />
            </div>

            <!-- Info -->
            <div class="col-7 d-flex flex-column justify-content-between p-3">
              <div>
                <h5 class="card-title mb-1 fw-semibold text-dark">
                  {{ booking.room.room_type }}
                </h5>
                <h6 class="card-subtitle mb-2 text-muted small">
                  Booking ID: {{ booking.booking_id }}
                </h6>

                <ul class="list-unstyled small mb-2">
                  <li><strong>Guest:</strong> {{ booking.user.name }}</li>
                  <li><strong>Check-in:</strong> {{ booking.check_in }}</li>
                  <li><strong>Check-out:</strong> {{ booking.check_out }}</li>
                  <li><strong>Total:</strong> ₹{{ booking.total_price }}</li>
                </ul>
              </div>

              <div class="d-flex justify-content-between align-items-center mt-2">
                <span :class="statusClass(booking.status)" class="badge rounded-pill px-3 py-1 text-capitalize">
                  {{ booking.status }}
                </span>

                <div class="d-flex gap-2">
                  <router-link :to="`/user/dashboard/booking/${booking.id}`" class="btn btn-sm btn-outline-primary">
                    View
                  </router-link>
                  <button v-if="booking.status === 'pending'" class="btn btn-sm btn-outline-danger">
                    Cancel
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center mt-5 text-muted">
      <p class="fs-5">No bookings found.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import axios from 'axios'

const store = useStore()
const getImageUrl = (path) => `/storage/${path}`
const bookings = ref([])

// Get user from Vuex store
const getUser = computed(() => store.getters.getUser)

// Fetch bookings from API
const fetchBookings = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get(`/api/bookings/${getUser.value.id}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    bookings.value = response.data.filter(b => b.user_id === getUser.value.id)
  } catch (error) {
    console.error('Error fetching bookings:', error)
  }
}

// Status badge class
const statusClass = (status) => {
  switch (status.toLowerCase()) {
    case 'confirmed': return 'bg-success text-white'
    case 'cancelled': return 'bg-danger text-white'
    case 'pending': return 'bg-warning text-dark'
    default: return 'bg-secondary text-white'
  }
}

onMounted(fetchBookings)
</script>
<style scoped>

</style>