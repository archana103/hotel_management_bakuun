<!-- HotelManagerNotifications.vue -->
<template>
  <div>
    <span v-if="notifications.length > 0" class="notification-count">
      {{ notifications.length }}
    </span>

    <div class="notifications-panel">
      <div v-if="loading" class="loading-spinner">Loading notifications...</div>
      <div v-else>
        <div v-if="notifications.length === 0" class="no-notifications">No notifications found.</div>
        <ul v-else>
          <li v-for="(notification, index) in notifications" :key="index" class="notification-item">
            <p class="font-semibold">{{ notification.data.message }}</p>
            <p class="text-sm text-gray-500">Received: {{ new Date(notification.created_at).toLocaleString() }}</p>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  
  // Initialize notifications as an empty array
  const notifications = ref([]);
  const loading = ref(true);
  
  // Fetch notifications from the API
  const fetchNotifications = async () => {
    try {
      const response = await axios.get('/api/notifications', {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      });
      console.warn('Notifications:', response.data.data);
      // Handle the case where data is missing or incorrectly formatted
      notifications.value = response.data.data || response.data || [];
    } catch (error) {
      console.error('Failed to fetch notifications:', error);
    } finally {
      loading.value = false; // Stop loading when the fetch is complete
    }
  };
  
  // Fetch notifications when the component is mounted
  onMounted(() => {
    fetchNotifications();
  });
  </script>
  
  <style scoped>
  .notification-item {
    border: 1px solid #ddd;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
  }
  
  .no-notifications {
    font-size: 16px;
    color: gray;
  }
  
  .loading-spinner {
    text-align: center;
    font-size: 18px;
  }
  </style>
  