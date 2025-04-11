<template>
  <div class="d-flex">
    <!-- Sidebar -->
    <div class="bg-dark text-white p-3" style="width: 250px;position: fixed;height: 100vh;">
      <h4 class="mb-4">Dashboard</h4>

      <!-- Common link -->
      <router-link to="/dashboard/overview" class="d-block mb-2 text-white">Overview</router-link>

      <!-- Master-specific -->
      <router-link v-if="hasPermission('manage admins')" to="/dashboard/admins"
        class="d-block mb-2 text-white">
        Manage Admins
      </router-link>
      <!-- Admin or Master -->
      <router-link v-if="hasPermission('create hotels') || hasPermission('view assigned hotels')" to="/dashboard/hotels" class="d-block mb-2 text-white">
        Manage Hotels
      </router-link>



      <button class="btn btn-outline-danger mt-3" @click="logoutAndRedirect"><i class="bi bi-box-arrow-left"></i>
        Logout</button>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4" style="margin-left:250px ;">
      <router-view />
    </div>
  </div>
</template>
<script setup>
import { useStore } from 'vuex';
import { computed } from 'vue';
import { useRouter } from 'vue-router';

const store = useStore();
const router = useRouter();

// Access Vuex getters
const getUser = computed(() => store.getters.getUser);
const hasPermission = computed(() => store.getters.hasPermission);

// Logout method
const logoutAndRedirect = () => {
  store.dispatch('logout');
  router.push('/');
};
</script>


<style scoped>
a.router-link-exact-active {
  font-weight: bold;
  text-decoration: underline;
}
</style>
