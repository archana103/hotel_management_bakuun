<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Manage Hotels</h2>
      <router-link to="/dashboard/hotels/create" class="btn btn-outline-primary">
        + Add Hotel
      </router-link>
    </div>


    <div v-if="hotels.data?.length">
      <table class="table table-striped">
        <thead>
          <tr>
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
            <td>{{ hotel.name }}</td>
            <td>{{ hotel.location }}</td>
            <td>
              <ul>
                <li v-for="a in hotel.amenities" :key="a.id">{{ a.name }}</li>
              </ul>
            </td>
            <td>{{ hotel.rooms.length }}</td>
            <td>
              <img v-for="img in hotel.images.slice(0, 3)" :key="img.id" :src="getImageUrl(img.image_path)"
                alt="hotel image" width="100" class="me-1" />
            </td>

            <td>
              <router-link :to="`/hotels/${hotel.id}`" class="btn btn-outline-primary btn-sm me-1">
                View
              </router-link>

              <button class="btn btn-outline-danger btn-sm" @click="deleteHotel(hotel.id)">
      Delete
    </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination Controls -->
      <nav>
        <ul class="pagination">
          <li class="page-item" :class="{ disabled: !hotels.prev_page_url }">
            <button class="page-link" @click="fetchHotels(hotels.prev_page_url)"
              :disabled="!hotels.prev_page_url">Previous</button>
          </li>
          <li class="page-item" :class="{ disabled: !hotels.next_page_url }">
            <button class="page-link" @click="fetchHotels(hotels.next_page_url)"
              :disabled="!hotels.next_page_url">Next</button>
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
import { ref, onMounted } from 'vue';
import axios from 'axios';

const hotels = ref({});

const fetchHotels = async (url = '/api/hotels') => {
  try {
    const token = localStorage.getItem('token');
    const response = await axios.get(url, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

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
