<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import PieChart from '@/charts/PieChart.vue';

// State variables
const from = ref('');
const to = ref('');
const chartData = ref(null);
const totalBookings = ref(0);
const totalRevenue = ref(0);

// Fetch function
const fetchStats = async () => {
  try {
    const params = {};
    if (from.value) params.from = from.value;
    if (to.value) params.to = to.value;

    const token = localStorage.getItem('token');

    const response = await axios.get('/api/reports/overview', {
      params,
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    const { daily, total_bookings, total_revenue } = response.data;

    const labels = Object.keys(daily);
    const bookings = labels.map(date => daily[date].bookings);

    chartData.value = {
      labels,
      datasets: [
        {
          label: 'Bookings per Day',
          data: bookings,
          backgroundColor: [
            '#FF6384', '#36A2EB', '#FFCE56',
            '#4BC0C0', '#9966FF', '#FF9F40'
          ],
          hoverOffset: 10,
        },
      ],
    };

    totalBookings.value = total_bookings;
    
    totalRevenue.value = total_revenue;

  } catch (error) {
    console.error('Failed to fetch report:', error);
  }
};

// Fetch stats when mounted
onMounted(fetchStats);
</script>

<template>
  <div>
    <h3>Booking Overview</h3>

    <!-- Date Filters -->
    <div class="mb-3">
      <label>From:</label>
      <input type="date" v-model="from" class="form-control d-inline w-auto mx-2" />
      <label>To:</label>
      <input type="date" v-model="to" class="form-control d-inline w-auto mx-2" />
      <button class="btn btn-primary" @click="fetchStats">Filter</button>
    </div>

    <!-- Chart -->
    <div v-if="chartData" style="height: 400px;">
      <PieChart :chart-data="chartData" />
    </div>
    <div v-else>
      <p>Loading data...</p>
    </div>

    <!-- Summary -->
    <div class="mt-4">
      <h5>Total Bookings: {{ totalBookings }}</h5>
      <h5>Total Revenue: ₹{{ totalRevenue }}</h5>
    </div>
  </div>
</template>
