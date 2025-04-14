<template>
  <div class="container">
    <div v-if="hotel" class="card shadow-sm p-4">
      <div class="col-md-12 mb-3">
        <div class="card shadow-sm" v-if="hotel.image">
          <img :src="getImageUrl(hotel.image)" class="card-img-top" alt="Main hotel image"
            style="height: 200px; object-fit: cover;" />
          <div class="card-body p-2 text-center">
            <p class="card-text  textcolor small mb-2">Main Image</p>

          </div>
        </div>

      </div>
      <div class="d-flex flex-wrap">
        <template v-if="hotel.images && hotel.images.length > 0">
          <h5 class="w-100 textcolor">Gallery</h5>
          <div class="d-flex flex-wrap">
            <div v-for="(img, index) in hotel.images" :key="img.id" class="position-relative me-2 mb-2"
              @click="openSlider(index)" style="cursor: pointer;">
              <img :src="getImageUrl(img.image_path)" alt="Hotel image" class="img-thumbnail"
                style="width: 100px; height: 100px; object-fit: cover;" />
            </div>
          </div>

          <!-- Modal for slider -->
          <div class="modal fade" ref="galleryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0">
                  <h5 class="modal-title">Gallery</h5>
                  <button type="button" class="btn-close btn-close-white" @click="closeSlider"></button>
                </div>
                <div class="modal-body p-0">
                  <div id="carouselGallery" class="carousel slide" data-bs-ride="carousel" ref="carousel">
                    <div class="carousel-inner">
                      <div v-for="(img, index) in hotel.images" :key="img.id"
                        :class="['carousel-item', { active: index === currentIndex }]">
                        <img :src="getImageUrl(img.image_path)" class="d-block w-100"
                          style="max-height: 600px; object-fit: contain;" alt="Gallery Image" />
                      </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselGallery"
                      data-bs-slide="prev">
                      <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselGallery"
                      data-bs-slide="next">
                      <span class="carousel-control-next-icon"></span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>

        <p v-else class="text-muted">No images available.</p>
      </div>

      <h2 class="mb-3  textcolor">{{ hotel.name }}</h2>
      <p class="text-muted mb-2">
        <i class="bi bi-geo-alt-fill "></i>{{ hotel.address }} ,{{
          hotel.location }}
      </p>

      <h5 class="d-inline  textcolor">Description</h5>
      <p class="mb-3">{{ hotel.description }}</p>

      <!-- Hotel Manager -->
      <div v-if="hotel.managers && hotel.managers.length">
        <h5 class="d-inline  textcolor">Managers</h5>
        <ul>
          <li v-for="m in hotel.managers" :key="m.id">
            {{ m.name }} ( {{ m.email }} )
          </li>
        </ul>
      </div>

      <!-- Amenities -->
      <div class="mb-3">
        <div class="mb-3">
          <strong class="textcolor">Amenities:</strong>
          <span v-if="hotel.amenities.length">
            <span v-for="(amenity, index) in hotel.amenities" :key="amenity.id">
              {{ amenity.name }}<span v-if="index < hotel.amenities.length - 1">, </span>
            </span>
          </span>
          <span v-else class="text-muted">No amenities listed.</span>
        </div>
        <span v-if="hotel.rooms.length">
          <div>
            <h5 class="fw-bold textcolor">Available Rooms:</h5>
            <ul class="list-group">
              <li v-for="room in hotel.rooms" :key="room.id"
                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                <div>
                  <strong>{{ room.room_type }}</strong> &mdash;
                  Capacity: {{ room.capacity }} guests &mdash;
                  ₹{{ room.price }} / night
                </div>

                <div class="d-flex align-items-center gap-2">
                  <span class="badge rounded-pill" style="font-size: medium;"
                    :class="room.current_status === 'available' ? 'bg-success' : 'bg-danger'">
                    {{ room.current_status }} For today
                  </span>
                  <button class="btn btn-outline-primary btn-sm" @click="openBookingModal(room)">
                    Book
                  </button>
                </div>
              </li>
            </ul>
          </div>
        </span>

        <span v-else class="text-muted">No rooms listed.</span>
      </div>


    </div>

    <div v-else class="text-center">
      <div class="spinner-border textcolor"></div>
      <p class="mt-2">Loading hotel details...</p>
    </div>
  </div>
  <!-- Booking Modal -->
  <div class="modal fade" tabindex="-1" ref="bookingModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Book Room</h5>
          <button type="button" class="btn-close" @click="closeModal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="submitBooking">
            <div class="mb-3">
              <label class="form-label">Check In</label>
              <input type="date" v-model="booking.check_in" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Check Out</label>
              <input type="date" v-model="booking.check_out" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Guests</label>
              <input type="number" v-model.number="booking.guests" class="form-control" :min="1"
                :max="selectedRoom?.capacity || 1" required />
              <small v-if="booking.guests > selectedRoom?.capacity" class="text-danger">
                Max guests allowed: {{ selectedRoom?.capacity }}
              </small>
            </div>


            <div v-if="booking.check_in && booking.check_out">
              <small :class="isAvailable ? 'text-success' : 'text-danger'">
                {{ isAvailable ? 'Room is available' : 'Room is not available for selected dates' }}
              </small>
            </div>

            <button type="submit" class="btn btn-primary w-100"
              :disabled="!isAvailable || !booking.check_in || !booking.check_out">
              Confirm Booking
            </button>

          </form>
        </div>
      </div>
    </div>
  </div>

</template>

<script setup>
import { ref, reactive, onMounted, nextTick, watch } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';
import { useStore } from 'vuex';
const galleryModal = ref(null);
const carousel = ref(null);
const isAvailable = ref(true);
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
  booking.room_id = room.id;
  booking.check_in = '';
  booking.check_out = '';
  booking.guests = room.capacity; // Set default guests to max capacity

  isAvailable.value = false;
  nextTick(() => {
    const modal = new bootstrap.Modal(bookingModal.value);
    modal.show();
    modalInstance.value = modal;
  });
};

const closeModal = () => {
  if (modalInstance.value) {
    modalInstance.value.hide();
  }
};

const submitBooking = async () => {
  if (booking.guests > selectedRoom.value.capacity) {
    alert(`Maximum guests allowed for this room is ${selectedRoom.value.capacity}`);
    return;
  }

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

watch(
  () => [booking.check_in, booking.check_out],
  async ([checkIn, checkOut]) => {
    if (!checkIn || !checkOut) return;

    const token = store.state.token;

    try {
      const response = await axios.post(
        '/api/check-availability',
        {
          room_id: selectedRoom.value.id,
          check_in: checkIn,
          check_out: checkOut,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (!response.data.available) {
        alert(response.data.message);
        isAvailable.value = false;
      } else {
        isAvailable.value = true;
      }
    } catch (err) {
      console.error('Error checking availability:', err);
    }
  }
);


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
<style>
.textcolor {
  color: #146b9c;
}
</style>