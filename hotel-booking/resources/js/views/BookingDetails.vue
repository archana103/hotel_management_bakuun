<template>
    <div class="container py-4" v-if="booking">
        <h2 class="mb-3 text-primary">Booking Details</h2>

        <div class="row">

            <div class="col-md-6 mb-4">
                <!-- <div v-if="hotel.images && hotel.images.length">
            <div v-for="(image, index) in hotel.images" :key="index" class="mb-3">
              <img :src="image" class="img-fluid rounded" alt="Hotel Image" />
            </div>
          </div>
          <div v-else>
            <p>No images available.</p>
          </div> -->
                <div class="mb-3">
                    <img src="/storage/hotel2.jpg" class="img-fluid rounded" alt="Hotel Image" />

                </div>



            </div>


            <div class="col-md-6">
                <h4 class="mb-2">{{ hotel.name || 'Hotel Name' }}</h4>
                <p><strong>Booking ID:</strong> {{ booking.booking_id }}</p>
                <p><strong>Room:</strong> {{ room.room_type }}</p>
                <p><strong>Guest:</strong> {{ booking.user.name }}</p>
                <p><strong>Check-in:</strong> {{ booking.check_in }}</p>
                <p><strong>Check-out:</strong> {{ booking.check_out }}</p>
                <p><strong>Total Price:</strong> ${{ booking.total_price }}</p>
                <p><strong>Status:</strong>
                    <span :class="statusClass(booking.status)" class="badge rounded-pill text-capitalize">
                        {{ booking.status }}
                    </span>
                </p>

                <!--         
          <div class="mt-3">
            <h5>Amenities</h5>
            <ul v-if="hotel.amenities && hotel.amenities.length">
              <li v-for="(item, index) in hotel.amenities" :key="index">{{ item }}</li>
            </ul>
            <p v-else>No amenities listed.</p>
          </div> -->

                <button class="btn btn-danger mt-4" @click="cancelBooking" v-if="booking.status === 'pending'">
                    Cancel Booking
                </button> <router-link to="/user/dashboard/my-bookings" class="btn btn-primary mt-4">
                    My Booking
                </router-link>
            </div>
        </div>
    </div>

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
