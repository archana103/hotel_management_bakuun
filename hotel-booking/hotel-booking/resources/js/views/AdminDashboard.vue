<template>
    <div class="container mt-5">
      <h2 class="text-success">Admin Dashboard</h2>
      <p>Welcome, Admin {{ user?.name }}! Manage users, bookings, and hotels here.</p>
      <button class="btn btn-danger" @click="handleLogout">Logout</button>
    </div>
  </template>
  
  <script>
  import { mapGetters, mapActions } from 'vuex';
  
  export default {
    computed: {
      ...mapGetters(['getUser'])
    },
    methods: {
      ...mapActions(['logout']),
      handleLogout() {
        this.logout();
        this.$router.push('/');
      }
    }
  };
  </script>
  