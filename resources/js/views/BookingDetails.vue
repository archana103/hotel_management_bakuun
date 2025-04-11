<template>
  <div class="container py-5" v-if="booking">
    <h2 class="mb-4 fw-bold" style="color: #146b9c">
      <i class="bi bi-receipt me-2"></i> Booking Details
    </h2>

    <div class="row g-4">
      <!-- Left Column: Images -->
      <div class="col-md-6">
        <!-- Main Image -->
        <div v-if="hotel.image" class="card shadow-sm rounded-4 overflow-hidden">
          <img
            :src="getImageUrl(hotel.image)"
            class="img-fluid"
            alt="Main hotel image"
            style="height: 300px; object-fit: cover;"
          />
        
        </div>
        <div v-else class="alert alert-secondary text-center">
          No main image available.
        </div>
      </div>

      <!-- Right Column: Booking Info -->
      <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <div class="card-body">
            <h4 class="card-title fw-bold text-dark mb-3">
              {{ hotel.name || 'Hotel Name' }}
            </h4>

            <ul class="list-unstyled small mb-4">
              <li><strong>Booking ID:</strong> {{ booking.booking_id }}</li>
              <li><strong>Room:</strong> {{ room.room_type }}</li>
              <li><strong>Guest:</strong> {{ booking.user.name }}</li>
              <li><strong>Check-in:</strong> {{ booking.check_in }}</li>
              <li><strong>Check-out:</strong> {{ booking.check_out }}</li>
              <li><strong>Total Price:</strong> ${{ booking.total_price }}</li>
              <li>
                <strong>Status:</strong>
                <span :class="statusClass(booking.status)" class="badge rounded-pill text-capitalize ms-1">
                  {{ booking.status }}
                </span>
              </li>
            </ul>

            <!-- Amenities -->
            <div class="mb-3">
              <h6 class="fw-semibold">Amenities</h6>
              <ul class="list-unstyled small mb-0" v-if="hotel.amenities && hotel.amenities.length">
                <li v-for="(item, index) in hotel.amenities" :key="index">
                  <i class="bi bi-check-circle-fill text-success me-1"></i> {{ item }}
                </li>
              </ul>
              <p class="text-muted small" v-else>No amenities listed.</p>
            </div>

            <!-- Actions -->
            <div class="d-flex flex-wrap mt-4">
              <button
                class="btn btn-outline-danger me-2 mb-2"
                @click="cancelBooking"
                v-if="booking.status === 'pending'"
              >
                Cancel Booking
              </button>
              <router-link to="/user/dashboard/my-bookings" class="btn btn-outline-primary mb-2">
                Back to My Bookings
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Loading State -->
  <div v-else class="text-center py-5">
    <div class="spinner-border text-primary" role="status"></div>
    <p class="mt-3">Loading booking details...</p>
  </div>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const booking = ref(null);
const room = ref({});
const hotel = ref({});
const getImageUrl = (path) => `/storage/${path}`
const route = useRoute();

const fetchBookingDetails = async () => {
  try {
    const token = localStorage.getItem('token');
    const bookingId = route.params.id;

    const res = await axios.get(`/api/bookings_details/${bookingId}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    booking.value = res.data;
    room.value = res.data.room;
    hotel.value = res.data.room.hotel;

    // Optional: convert amenities/images to arrays if needed
    // hotel.value.images = res.data.room.hotel.images?.split(',') || [];
    // hotel.value.amenities = res.data.room.hotel.amenities?.split(',') || [];

  } catch (err) {
    console.error('Error loading booking:', err);
  }
};

const cancelBooking = async () => {
  if (!confirm('Are you sure you want to cancel this booking?')) return;

  try {
    const token = localStorage.getItem('token');
    await axios.put(`/api/booking/${booking.value.id}/cancel`, {}, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    booking.value.status = 'cancelled';
    alert('Booking cancelled successfully.');
  } catch (error) {
    console.error('Cancel failed:', error);
    alert('Something went wrong while cancelling.');
  }
};

const statusClass = (status) => {
  switch (status.toLowerCase()) {
    case 'confirmed': return 'bg-success text-white';
    case 'cancelled': return 'bg-danger text-white';
    case 'pending': return 'bg-warning text-dark';
    default: return 'bg-secondary text-white';
  }
};

onMounted(fetchBookingDetails);
</script>
