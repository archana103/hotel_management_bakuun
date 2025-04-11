<template>
  <div class="container py-4">

    <!-- Search Heading -->
    <h2 class="mb-4 fw-bold textcolor">
      <i class="bi bi-search me-2"></i>Search Hotels
    </h2>

    <!-- Filter Card -->
    <div class="card p-4 mb-4 shadow-sm border-0 bg-light">
      <div class="row gy-3 gx-4 align-items-end">

        <div class="col-md-3">
          <label class="form-label">Location</label>
          <input type="text" v-model="filters.location" placeholder="Enter Location" class="form-control" />
        </div>

        <div class="col-md-3">
          <label class="form-label d-flex justify-content-between">
            Max Price (₹)
            <span class="text-muted small">{{ filters.max_price }}</span>
          </label>
          <input type="range" min="0" max="10000" step="50" v-model="filters.max_price" class="form-range" />
        </div>

        <div class="col-md-2">
          <label class="form-label">Guests</label>
          <input type="number" min="1" v-model="filters.guests" placeholder="Guests" class="form-control" />
        </div>

        <div class="col-md-2">
          <label class="form-label">Check-in</label>
          <input type="date" v-model="filters.check_in" class="form-control" />
        </div>

        <div class="col-md-2">
          <label class="form-label">Check-out</label>
          <input type="date" v-model="filters.check_out" class="form-control" />
        </div>

        <!-- Amenities Filter -->
        <div class="col-12 mt-3">
          <label class="form-label fw-semibold">Amenities</label>
          <div class="d-flex flex-wrap gap-3">
            <div v-for="amenity in availableAmenities" :key="amenity.id" class="form-check me-3">
              <input type="checkbox" :value="amenity.id" v-model="filters.amenities" class="form-check-input" :id="`amenity-${amenity.id}`" />
              <label class="form-check-label" :for="`amenity-${amenity.id}`">
                {{ amenity.name }}
              </label>
            </div>
          </div>
        </div>

        <!-- Buttons -->
        <div class="col-12 d-flex justify-content-center mt-4">
          <button @click="fetchHotels" class="btn btn-primary px-5 me-3">
            <i class="bi bi-search"></i> Search Hotels
          </button>
          <button @click="clearResults" class="btn btn-outline-secondary px-5">
            <i class="bi bi-x-lg"></i> Clear Results
          </button>
        </div>

      </div>
    </div>

    <!-- Loader -->
    <div v-if="loading" class="text-center text-primary fw-semibold py-5">
      <div class="spinner-border text-primary mb-2" role="status"></div>
      <p>Loading hotels...</p>
    </div>

    <!-- No Results -->
    <div v-else-if="hotels.length === 0" class="text-center my-5 text-muted">
      <i class="bi bi-buildings fs-1 d-block mb-2"></i>
      No hotels match your search.
    </div>

    <!-- Results -->
    <div v-else class="row gy-4">
      <div v-for="hotel in hotels" :key="hotel.id" class="col-md-6">
        <div class="card shadow-sm border-0 overflow-hidden flex-md-row h-100">
          
          <!-- Hotel Image -->
          <img :src="getImageUrl(hotel.image)" alt="Hotel Image"
               class="hotel-thumb object-fit-cover" 
               style="width: 250px; height: 200px; object-fit: cover;" />

          <!-- Hotel Info -->
          <div class="card-body d-flex flex-column justify-content-between">
            <div>
              <h5 class="card-title mb-1 fw-bold">{{ hotel.name }}</h5>
              <p class="text-muted small mb-2">
                <i class="bi bi-geo-alt-fill me-1"></i>{{ hotel.location }}
              </p>
              <p class="small text-muted mb-2">
                <strong>Amenities:</strong>
                <span v-for="(amenity, index) in hotel.amenities" :key="amenity.id">
                  {{ amenity.name }}<span v-if="index < hotel.amenities.length - 1">, </span>
                </span>
              </p>
              <p v-if="hotel.rooms.length" class="fw-bold text-primary mb-2">
                From ₹{{ getMinPrice(hotel.rooms) }} / night
              </p>
              <p v-else class="text-muted">No rooms available</p>
            </div>

            <!-- Room List -->
            <div v-if="hotel.rooms.length">
              <p class="fw-bold mb-1">Available Rooms:</p>
              <ul class="list-unstyled small mb-2">
                <li v-for="room in hotel.rooms" :key="room.id" class="text-muted">
                  {{ room.name }} – Capacity: {{ room.capacity }}, Price: ₹{{ room.price }}
                </li>
              </ul>
                <!-- View Button -->
            <router-link :to="`/user/dashboard/hotelsview/${hotel.id}`" class="btn btn-outline-primary btn-sm align-self-start mt-2">
              View Hotel
            </router-link>
            </div>

          
          </div>
        </div>
      </div>
    </div>

  </div>
</template>




<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useStore } from 'vuex';

const store = useStore();

const hotels = ref([]);
const availableAmenities = ref([]);
const loading = ref(false);

const filters = ref({
  location: '',
  min_price: '',
  max_price: '',
  check_in: '',
  check_out: '',
  guests: '',
  amenities: [],
});

const getImageUrl = (path) => `/storage/${path}`;

const fetchHotels = async () => {
  loading.value = true;
  try {
    const token = store.state.token;

    const response = await axios.post(
      '/api/hotels/search',
      {
        location: filters.value.location,
        min_price: filters.value.min_price,
        max_price: filters.value.max_price,
        check_in: filters.value.check_in,
        check_out: filters.value.check_out,
        guests: filters.value.guests,
        amenities: filters.value.amenities.length ? filters.value.amenities : null,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json',
        },
      }
    );

    hotels.value = response.data.hotels;

    // Save filters and results to localStorage
    localStorage.setItem('hotelFilters', JSON.stringify(filters.value));
    localStorage.setItem('hotelResults', JSON.stringify(hotels.value));

  } catch (error) {
    console.error('Error fetching hotels:', error);
    if (error.response?.status === 401) {
      store.dispatch('setError', 'Session expired. Please log in again.');
      store.dispatch('logout');
    }
  } finally {
    loading.value = false;
  }
};

const fetchAmenities = async () => {
  try {
    const token = store.state.token;

    const response = await axios.get('/api/amenities', {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
    });

    availableAmenities.value = response.data;

  } catch (error) {
    console.error('Error fetching amenities:', error);
    if (error.response?.status === 401) {
      store.dispatch('setError', 'Session expired. Please log in again.');
      store.dispatch('logout');
    }
  }
};
const clearResults = () => {
  // Reset filters
  filters.value = {
    location: '',
    min_price: '',
    max_price: '',
    check_in: '',
    check_out: '',
    guests: '',
    amenities: [],
  };

  // Clear results
  hotels.value = [];

  // Remove from localStorage
  localStorage.removeItem('hotelFilters');
  localStorage.removeItem('hotelResults');
};

const getMinPrice = (rooms) => {
  if (!rooms.length) return 'N/A';
  return Math.min(...rooms.map((room) => room.price));
};

onMounted(() => {
  fetchAmenities();

  // Load saved filters
  const savedFilters = localStorage.getItem('hotelFilters');
  if (savedFilters) {
    filters.value = JSON.parse(savedFilters);
  }

  // Load saved results
  const savedResults = localStorage.getItem('hotelResults');
  if (savedResults) {
    hotels.value = JSON.parse(savedResults);
  }
});

</script>

<style>
.hotel-thumb {
  border-top-left-radius: 0.375rem;
  border-bottom-left-radius: 0.375rem;
}
.textcolor { 
  color: #146b9c;
}
</style>