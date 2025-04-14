<template>
  <div class="d-flex">

    <div class="bg-dark text-white p-3" style="width: 250px; position: fixed; height: 100vh;">
      <h4 class="mb-5"><i class="bi bi-buildings-fill"></i> BookStay</h4>

      <router-link to="/dashboard/overview" class="d-block mb-2 text-white">Overview</router-link>

      <!-- Master-specific -->
      <router-link v-if="hasPermission('manage admins')" to="/dashboard/admins" class="d-block mb-2 text-white">
        Manage Admins
      </router-link>
      
      <!-- Admin or Master -->
      <router-link v-if="hasPermission('create hotels') || hasPermission('view assigned hotels')" to="/dashboard/hotels"
        class="d-block mb-2 text-white">
        Manage Hotels
      </router-link>
      
      <router-link v-if="hasPermission('create bookings') || hasPermission('view bookings')" to="/dashboard/bookings"
        class="d-block mb-2 text-white">
        Manage Bookings
      </router-link>

      <button class="btn btn-outline-danger mt-3" @click="logoutAndRedirect"><i class="bi bi-box-arrow-left"></i> Logout</button>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4" style="margin-left: 250px;">
      <!-- Welcome Message -->
      <div v-if="showWelcome" class="alert alert-success">
        👋 Welcome, {{ getUser?.name || 'User' }}!
      </div>
      <span v-if="notifications.length > 0" class="notification-count">
      {{ notifications.length }}
    </span>
      <!-- Notifications for Hotel Managers -->
      <HotelManagerNotifications v-if="getUser?.role === 'Hotel Manager'" />

      <!-- Dynamic Router View -->
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
import HotelManagerNotifications from '@/components/HotelManagerNotifications.vue';  // Import Notifications Component

const store = useStore();
const router = useRouter();

const getUser = computed(() => store.getters.getUser);
const hasPermission = computed(() => store.getters.hasPermission);

const showWelcome = ref(true);

const logoutAndRedirect = () => {
  store.dispatch('logout');
  router.push('/');
};

onMounted(() => {
  setTimeout(() => {
    showWelcome.value = false;
  }, 10 * 1000); // 10 seconds
});
</script>

<style scoped>
a.router-link-exact-active {
  font-weight: bold;
  text-decoration: underline;
}
</style>
