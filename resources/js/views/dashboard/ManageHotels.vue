<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Manage Hotels</h2>
      <router-link v-if="hasPermission('create hotels')" to="/dashboard/hotels/create" class="btn btn-outline-primary">
        + Add Hotel
      </router-link>
    </div>


    <div v-if="hotels.data?.length">
      <div class="table-responsive">
        <table class="table table-bordered align-middle table-hover shadow-sm">
          <thead class="table-light">
            <tr class="text-center">
              <th scope="col">ID</th>
              <th>Name</th>
              <th>Location</th>
              <th>Amenities</th>
              <th>Rooms</th>
              <th>Images</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="hotel in hotels.data" :key="hotel.id">
              <td class="text-center">{{ hotel.id }}</td>
              <td>
                <div class="d-flex align-items-center gap-3">
                  <img :src="getImageUrl(hotel.image)" alt="Hotel" width="70" height="60"
                    class="rounded shadow-sm border object-fit-cover" style="object-fit: cover;" />
                  <span class="fw-semibold">{{ hotel.name }}</span>
                </div>
              </td>
              <td>{{ hotel.location }}</td>

              <!-- Amenities -->
              <td>
                <ul v-if="hotel.amenities && hotel.amenities.length" class="mb-0 ps-3 small">
                  <li v-for="a in hotel.amenities" :key="a.id">{{ a.name }}</li>
                </ul>
                <span v-else class="text-muted small">No amenities</span>
              </td>


              <!-- Rooms -->
              <td>
                <ul v-if="hotel.rooms?.length" class="mb-0 ps-3 small">
                  <li v-for="room in hotel.rooms" :key="room.id">{{ room.room_type }}</li>
                </ul>
                <span v-else class="text-muted small">No rooms</span>
              </td>

              <!-- Images -->
              <td>
                <div v-if="hotel.images && hotel.images.length" class="d-flex flex-wrap align-items-center gap-1">
                  <img v-for="img in hotel.images.slice(0, 3)" :key="img.id" :src="getImageUrl(img.image_path)"
                    alt="Hotel" width="80" height="60" class="rounded shadow-sm border object-fit-cover" />
                </div>
                <span v-else class="text-muted small">No images</span>
              </td>


              <!-- Actions -->
              <td class="text-nowrap">
                <router-link :to="`/hotels/${hotel.id}`" class="btn btn-sm btn-outline-primary me-1" v-if="hasPermission('edit hotels')">
                  View
                </router-link>
                <button v-if="hasPermission('delete hotels')" class="btn btn-sm btn-outline-danger" @click="deleteHotel(hotel.id)">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Controls -->
      <nav class="mt-4 d-flex justify-content-center">
        <ul class="pagination">
          <li class="page-item" :class="{ disabled: !hotels.prev_page_url }">
            <button class="page-link" @click="fetchHotels(hotels.prev_page_url)" :disabled="!hotels.prev_page_url">
              Previous
            </button>
          </li>
          <li class="page-item" :class="{ disabled: !hotels.next_page_url }">
            <button class="page-link" @click="fetchHotels(hotels.next_page_url)" :disabled="!hotels.next_page_url">
              Next
            </button>
          </li>
        </ul>
      </nav>
    </div>

    <div v-else>
      <p>No hotels found.</p>
    </div>
  </div>
</template>

<script setup>
const getImageUrl = (path) => {
  return `/storage/${path}`;
};
import { ref, onMounted,computed } from 'vue';
import axios from 'axios';
import { useStore } from 'vuex';

const store = useStore();
const hotels = ref({});
const hasPermission = computed(() => store.getters.hasPermission);
const fetchHotels = async (url = null) => {
  try {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));

    let apiUrl = url || '/api/hotels';

    // Check if user is NOT a Master
    const isMaster = user.roles?.some(role => role.name.toLowerCase() === 'master');

    if (!isMaster) {
      apiUrl = url || `/api/hotels_manager/${user.id}`;
    }

    const response = await axios.get(apiUrl, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
console.log(apiUrl);
    hotels.value = response.data;
  } catch (error) {
    console.error('Error fetching hotels:', error);
  }
};


const deleteHotel = async (id) => {
  if (!confirm('Are you sure you want to delete this hotel?')) return

  try {
    const token = localStorage.getItem('token')
    await axios.delete(`/api/hotels/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    fetchHotels() // refresh the list after deletion
  } catch (err) {
    console.error('Delete hotel failed:', err.response?.data || err)
  }
};

onMounted(() => {
  fetchHotels();
});
</script>
