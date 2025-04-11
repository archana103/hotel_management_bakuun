import { createRouter, createWebHistory } from "vue-router";
import store from "../store";

import Login from "@/views/Login.vue";
import Register from "@/views/Register.vue";

import UserDashboard from "@/views/UserDashboard.vue";
import SearchHotel from "@/views/SearchHotel.vue";
import MyBookings from "@/views/MyBookings.vue";
import MyProfile from "@/views/MyProfile.vue";
import BookingDetails from "@/views/BookingDetails.vue";
import HotelDetails from "@/views/HotelDetails.vue";

// Shared Admin/Master dashboard layout
import DashboardLayout from "@/views/MasterDashboard.vue";
import Overview from "@/views/dashboard/Overview.vue";
import ManageAdmins from "@/views/dashboard/ManageAdmins.vue";
import ManageHotels from "@/views/dashboard/ManageHotels.vue";
import ManageBookings from "@/views/dashboard/ManageBookings.vue";
import HotelDetailsPage from'@/views/dashboard/HotelDetailsPage.vue';
const routes = [
  { path: "/", component: Login },
  { path: "/register", component: Register },
  
  {
    path: "/user/dashboard",
    component: UserDashboard,
    meta: { requiresAuth: true, role: "users" },
    children: [
      { path: "search-hotel", component: SearchHotel },
      { path: "my-bookings", component: MyBookings },
      { path: "my-profile", component: MyProfile },
      { path: "booking/:id", component: BookingDetails },
      {
        path: '/user/dashboard/hotelsview/:id',
        name: 'HotelDetails',
        component: () => import('@/views/HotelDetails.vue') // or whatever file you're using
      }
      
      
    ],
  },

  // Shared route for admin & master
  {
    path: "/dashboard",
    component: DashboardLayout,
    meta: { requiresAuth: true, role: ["admin", "master"] },
    children: [
      { path: "overview", component: Overview },
      { path: "admins", component: ManageAdmins },
      { path: "hotels", component: ManageHotels },
      { path: "bookings", component: ManageBookings },
      { path: "/hotels/:id", component: HotelDetailsPage },
      {
        path: "/dashboard/hotels/create",
        component: () => import('@/views/dashboard/CreateHotel.vue'),
        meta: { requiresAuth: true, role: ["admin", "master"] } // or "master" if needed
      },
    ],
  },

];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = store.getters.isAuthenticated;
  const userRole = store.getters.getRole;
  const token = localStorage.getItem("token");
  const isTokenValid = token && token !== "undefined" && token !== "null";
  if ((to.path === "/" || to.path === "/register") && isTokenValid) {
    if (userRole === "master" || userRole === "admin") {
      return next("/dashboard/overview");
    } else if (userRole === "users") {
      return next("/user/dashboard/search-hotel");
    }
  }
  if (to.meta.requiresAuth && !token) {
    return next("/");
  }

  if (to.meta.requiresAuth && !isAuthenticated) {
    return next("/");
  }

  if (to.meta.role) {
    const allowedRoles = Array.isArray(to.meta.role) ? to.meta.role : [to.meta.role];
    if (!allowedRoles.includes(userRole)) {
      return next("/");
    }
  }

  next();
});

export default router;
