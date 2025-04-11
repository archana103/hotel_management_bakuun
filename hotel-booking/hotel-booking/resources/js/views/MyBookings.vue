<template>
  <div class="container py-4">
    <h2 class="mb-4 text-primary">My Bookings</h2>

    <div class="row g-4" v-if="bookings.length">
      <div class="col-sm-12 col-md-6 col-lg-6" v-for="booking in bookings" :key="booking.id">
        <div class="card shadow-sm">
          <div class="card-body " style="display: flex;gap:20px">
            <div style="display:flex;flex-direction: column;">
              <h5 class="card-title">{{ booking.room.room_type }}</h5>
              <h6 class="card-subtitle mb-2 text-muted">Booking ID: {{ booking.booking_id }}</h6>

              <ul class="list-unstyled mt-3 mb-3 small">
                <li><strong>Guest : </strong> {{ booking.user.name }}</li>
                <li><strong>Check-in : </strong> {{ booking.check_in }}</li>
                <li><strong>Check-out : </strong> {{ booking.check_out }}</li>
                <li><strong>Total Price : </strong>{{ booking.total_price }} Rs</li>
              </ul>

              <span :class="statusClass(booking.status)" class="badge rounded-pill text-capitalize" style="width:100px">
                {{ booking.status }}
              </span>
            </div>
            <div style="width: 60%;"> <img src="/storage/hotel2.jpg" class="img-fluid rounded" alt="Hotel Image" />
            </div>
          </div>
          <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
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

    <div v-else class="text-center mt-5">
      <p>No bookings found.</p>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'

export default {
  data() {
    return {
      bookings: []
    }
  },
  computed: {
    ...mapGetters(['getUser']) 
  },
  methods: {
    async fetchBookings() {
      try {

        const token = localStorage.getItem('token');
        const response = await axios.get(`/api/bookings/${this.getUser.id}`, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        this.bookings = response.data.filter(b => b.user_id === this.getUser.id); 
      } catch (error) {
        console.error('Error fetching bookings:', error);
      }
    },
    statusClass(status) {
      switch (status.toLowerCase()) {
        case 'confirmed': return 'bg-success text-white';
        case 'cancelled': return 'bg-danger text-white';
        case 'pending': return 'bg-warning text-dark';
        default: return 'bg-secondary text-white';
      }
    }
  }
  ,
  mounted() {
    this.fetchBookings()
  }
}
</script>
