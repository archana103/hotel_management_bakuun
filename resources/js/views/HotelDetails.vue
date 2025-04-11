<template>
  <div class="container">
    <div v-if="booking" class="card shadow-sm p-4">
      
      <!-- Hotel Main Image -->
      <div class="col-md-12 mb-3">
        <div class="card shadow-sm" v-if="hotel.image">
          <img :src="getImageUrl(hotel.image)" class="card-img-top" alt="Main hotel image"
            style="height: 200px; object-fit: cover;" />
          <div class="card-body p-2 text-center">
            <p class="card-text text-primary small mb-2">Main Image</p>
          </div>
        </div>
      </div>

      <!-- Booking Info -->
      <h2 class="mb-3 text-primary">Booking Details</h2>
      <p><strong>Booking ID:</strong> {{ booking.booking_id }}</p>
      <p><strong>Room:</strong> {{ room.room_type }}</p>
      <p><strong>Guest:</strong> {{ booking.user.name }}</p>
      <p><strong>Check-in:</strong> {{ booking.check_in }}</p>
      <p><strong>Check-out:</strong> {{ booking.check_out }}</p>
      <p><strong>Total Price:</strong> ₹{{ booking.total_price }}</p>
      <p><strong>Status:</strong>
        <span :class="statusClass(booking.status)" class="badge rounded-pill text-capitalize">
          {{ booking.status }}
        </span>
      </p>

      <!-- Amenities -->
      <div class="mt-3 mb-2">
        <h5 class="text-primary">Amenities</h5>
        <ul v-if="hotel.amenities && hotel.amenities.length">
          <li v-for="(item, index) in hotel.amenities" :key="index">{{ item }}</li>
        </ul>
        <p v-else class="text-muted">No amenities listed.</p>
      </div>

      <!-- Actions -->
      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-danger" @click="cancelBooking" v-if="booking.status === 'pending'">
          Cancel Booking
        </button>
        <router-link to="/user/dashboard/my-bookings" class="btn btn-primary">
          My Bookings
        </router-link>
      </div>

    </div>

    <div v-else class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-3">Loading booking details...</p>
    </div>
  </div>
</template>


<script setup>
import { ref, reactive, onMounted, nextTick } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';
import { useStore } from 'vuex';
const galleryModal = ref(null);
const carousel = ref(null);
const currentIndex = ref(0);
const hotel = ref(null);
const selectedRoom = ref(null);
const booking = reactive({
  check_in: '',
  check_out: '',
  guests: 1,
});
const getImageUrl = (path) => `/storage/${path}`
const modalInstance = ref(null);
const bookingModal = ref(null);

const route = useRoute();
const store = useStore();
const openSlider = (index) => {
  currentIndex.value = index;

  nextTick(() => {
    const modal = new bootstrap.Modal(galleryModal.value);
    modal.show();

    const carouselEl = carousel.value;
    const carouselInstance = bootstrap.Carousel.getOrCreateInstance(carouselEl);
    carouselInstance.to(index);
  });
};

const closeSlider = () => {
  const modal = bootstrap.Modal.getInstance(galleryModal.value);
  if (modal) modal.hide();
};

const openBookingModal = (room) => {
  selectedRoom.value = room;
  booking.check_in = '';
  booking.check_out = '';
  booking.guests = 1;

  const modal = new bootstrap.Modal(bookingModal.value);
  modal.show();
  modalInstance.value = modal;
};

const closeModal = () => {
  if (modalInstance.value) {
    modalInstance.value.hide();
  }
};

const submitBooking = async () => {
  try {
    const token = store.state.token;

    const payload = {
      room_id: selectedRoom.value.id,
      check_in: booking.check_in,
      check_out: booking.check_out,
      guests: booking.guests,
    };

    await axios.post('/api/bookings', payload, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    alert('Booking successful!'); 
    closeModal();
  
  } catch (err) {
    console.error('Booking failed:', err);
    alert('Failed to book room. Please try again.');
  }
};

const fetchRooms = async () => {
  const token = store.state.token;
  const hotelId = route.params.id;
  try {
    const res = await axios.get(`/api/hotels/${hotelId}/rooms`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    const roomList = res.data;

for (const room of roomList) {
  const availability = await axios.get(`/api/rooms/${room.id}/availability`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  room.current_status = availability.data.status;

}

// Or log entire list after loop


// Assign to hotel.rooms (make sure `hotel.value.rooms` exists)
hotel.value.rooms = roomList;

  } catch (err) {
    console.error('Failed to fetch rooms or availability:', err)
  }
}

// Initial hotel fetch
onMounted(async () => {
  try {
    const token = store.state.token;
    const id = route.params.id;

    const res = await axios.get(`/api/hotels/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });

    hotel.value = res.data;

    // Fetch and attach rooms with availability after hotel is loaded
    await fetchRooms();

  } catch (err) {
    console.error('Failed to load hotel:', err);
  }
});
</script>