<template>
  <div class="dashboard-container">
    <aside class="sidebar shadow">
      <div class="sidebar-header mb-4">
  <h4><i class="bi bi-buildings-fill"></i> HotelMate</h4>
</div>
      <ul class="nav flex-column">
        <li :class="{ active: isActive('/user/dashboard/search-hotel') }">
          <router-link to="/user/dashboard/search-hotel">
            <i class="bi bi-search me-2"></i>Search Hotels
          </router-link>
        </li>
        <li :class="{ active: isActive('/user/dashboard/my-bookings') }">
          <router-link to="/user/dashboard/my-bookings">
            <i class="bi bi-calendar-check me-2"></i>My Bookings
          </router-link>
        </li>
        <li :class="{ active: isActive('/user/dashboard/my-profile') }">
          <router-link to="/user/dashboard/my-profile">
            <i class="bi bi-person-circle me-2"></i>My Profile
          </router-link>
        </li>
        <li>
          <button class="btn logout-btn mt-3 w-100" @click="handleLogout">
            <i class="bi bi-box-arrow-left me-2"></i>Logout
          </button>
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
  background-color: #fefefe; /* soft white background */
}

.sidebar {
  width: 260px;
  background: linear-gradient(to bottom, #f2f9ff, #ffffff); /* baby blue gradient */
  padding: 30px 20px;
  display: flex;
  flex-direction: column;
  position: fixed;
  height: 100vh;
  border-right: 1px solid #e0e0e0;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.03);
}

.sidebar-header {
  text-align: center;
  font-size: 1.5rem;
  letter-spacing: 1px;
  font-weight: bold;
  color: #146b9c; /* lavender accent */
  margin-bottom: 30px;
}
.textcolor { 
  color: #146b9c;
}
.sidebar ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar ul li {
  margin-bottom: 14px;
}

.sidebar ul li a {
  text-decoration: none;
  color: #2e4053; /* navy muted */
  display: flex;
  align-items: center;
  padding: 10px 16px;
  border-radius: 12px;
  font-weight: 500;
  font-size: 1rem;
  transition: all 0.3s ease;
  background-color: transparent;
}

.sidebar ul li a:hover {
  background-color: #dee9ee; /* mint hover */
  color: #146b9c; /* darker mint text */
}

.sidebar ul li.active a {
  background-color: #146b9c;
  color: #ffffff;
  font-weight: 600;
  box-shadow: inset 0 0 0 2px #82c4df;
}

/* Logout Button */
.logout-btn {
  background-color: #f8d7da; /* baby pink */
  color: #842029;
  border: none;
  padding: 10px 16px;
  border-radius: 12px;
  font-weight: 500;
  width: 100%;
  transition: background-color 0.3s ease;
}

.logout-btn:hover {
  background-color: #f5c6cb;
  color: #6c1b24;
}

/* Main Content */
.content {
  flex: 1;
  padding: 30px;
  margin-left: 260px;
  background-color: #ffffff;
}


</style>
