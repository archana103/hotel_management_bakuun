<template>
  <div>
    <h2>Manage Bookings</h2>

    <!-- Filters -->
    <div class="mb-3 d-flex gap-3 align-items-end">
      <div>
        <label>From:</label>
        <input type="date" v-model="fromDate" class="form-control" />
      </div>
      <div>
        <label>To:</label>
        <input type="date" v-model="toDate" class="form-control" />
      </div>
      <button class="btn btn-primary" @click="fetchBookings">Filter</button>
    </div>

    <!-- Booking Table -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Hotel</th>
          <th>Room</th>
          <th>Guest</th>
          <th>From</th>
          <th>To</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="booking in bookings" :key="booking.id">
          <td>{{ booking.hotel.name }}</td>
          <td>{{ booking.room.name }}</td>
          <td>{{ booking.user.name }}</td>
          <td>{{ booking.start_date }}</td>
          <td>{{ booking.end_date }}</td>
          <td>{{ booking.status }}</td>
          <td>
            <select v-model="booking.status" @change="updateStatus(booking)" class="form-select">
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const bookings = ref([]);
const fromDate = ref('');
const toDate = ref('');

const user = JSON.parse(localStorage.getItem('user'));
const token = localStorage.getItem('token');

const fetchBookings = async () => {
  try {
    let apiUrl = '/api/bookings';

    if (user?.roles?.[0]?.name === 'Admin') {
      apiUrl = `/api/bookings/admin/${user.id}`;
    }

    const params = {};
    if (fromDate.value) params.from = fromDate.value;
    if (toDate.value) params.to = toDate.value;

    const response = await axios.get(apiUrl, {
      params,
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    bookings.value = response.data;
  } catch (error) {
    console.error('Error fetching bookings:', error);
  }
};

const updateStatus = async (booking) => {
  try {
    await axios.put(`/api/bookings/${booking.id}/status`, {
      status: booking.status,
    }, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
  } catch (error) {
    console.error('Error updating status:', error);
  }
};

onMounted(fetchBookings);
</script>
