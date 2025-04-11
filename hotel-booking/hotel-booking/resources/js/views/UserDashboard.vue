<template>
  <div class="dashboard-container">

    <aside class="sidebar">
      <ul style="">

        <li :class="{ active: isActive('/user/dashboard/search-hotel') }">
          <router-link to="/user/dashboard/search-hotel">Search Hotel</router-link>
        </li>
        <li :class="{ active: isActive('/user/dashboard/my-bookings') }">
          <router-link to="/user/dashboard/my-bookings">My Bookings</router-link>
        </li>
        <li :class="{ active: isActive('/user/dashboard/my-profile') }">
          <router-link to="/user/dashboard/my-profile">My Profile</router-link>
        </li>
        <li>
          <button class="btn btn-outline-danger" @click="handleLogout"><i class="bi bi-box-arrow-left"></i> Logout</button>
        </li>
      </ul>
    </aside>


    <main class="content">
      <router-view></router-view>
    </main>
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
    },

    isActive(route) {
      return this.$route.path === route;
    }
  }
};
</script>

<style scoped>
.dashboard-container {
  display: flex;
  min-height: 100vh;
}

/* Sidebar Styles */
.sidebar {
  width: 250px;
  background: #d6e6f5;
  padding: 20px;
  display: flex;
  flex-direction: column;
  position: fixed;
  height: 100vh;
  z-index: 1;
}

.sidebar ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar ul li {
  margin-bottom: 10px;
}

.sidebar ul li a {
  text-decoration: none;
  color: #7b8491;
  display: block;
  padding: 10px;
  border-radius: 5px;
  transition: background 0.3s ease-in-out;
  font-size: larger;
  font-weight: 700;
}

/* Active Link Styling */
.sidebar ul li.active a {
  border-bottom: #0a58ca6b solid;
  color: #22a0af;

}

/* Logout Button */
.logout-btn {
  background: #0a58ca6b;
  color: white;
  border: none;
  padding: 10px;
  width: 100%;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s ease-in-out;
}

.logout-btn:hover {
  background: #0a58ca;
}

/* Content Styles */
.content {
  flex: 1;
  padding: 20px;
  margin-left: 250px;
}
</style>
