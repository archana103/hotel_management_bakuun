<template>
  <div class="d-flex">
    <!-- Sidebar -->
    <div class="bg-dark text-white p-3" style="width: 250px;position: fixed;height: 100vh;">
      <h4 class="mb-4">Dashboard</h4>

      <!-- Common link -->
      <router-link to="/dashboard/overview" class="d-block mb-2 text-white">Overview</router-link>

      <!-- Master-specific -->
      <router-link
        v-if="hasPermission('manage admins')"
        to="/dashboard/admins"
        class="d-block mb-2 text-white"
      >
        Manage Admins
      </router-link>

      <!-- Admin or Master -->
      <router-link
        v-if="hasPermission('create hotels')"
        to="/dashboard/hotels"
        class="d-block mb-2 text-white"
      >
        Manage Hotels
      </router-link>

     

      <button class="btn btn-outline-danger mt-3" @click="logoutAndRedirect"><i class="bi bi-box-arrow-left"></i> Logout</button>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4" style="margin-left:250px ;">
      <router-view />
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
  computed: {
    ...mapGetters(['getUser', 'hasPermission']),
  },
  methods: {
    ...mapActions(['logout']),
    logoutAndRedirect() {
      this.logout();
      this.$router.push('/');
    }
  }
};
</script>

<style scoped>
a.router-link-exact-active {
  font-weight: bold;
  text-decoration: underline;
}
</style>
