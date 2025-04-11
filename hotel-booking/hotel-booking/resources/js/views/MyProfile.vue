<template>
  <div class="container mt-4">
    <h2 class="mb-4 text-primary">My Profile</h2>

    <div class="card p-4 mb-4 shadow-sm">
      <div class="row g-3">
        <form @submit.prevent="updateProfile">
          <div class="col-md-6">
            <label for="username">User Name</label>
            <input type="text" class="form-control" id="username" v-model="username" required />
          </div>
          <div class="col-md-6">
            <label for="email">Email Id</label>
            <input type="email" class="form-control" id="email" v-model="email" required />
          </div>
          <div class="col-md-6">
            <label for="password">Password</label>
            <input type="password" class="form-control" v-model="password" id="password" placeholder="Update Your Password"/>
          </div>
          <button type="submit" class="btn btn-primary mt-3">
            Update
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
<script>
import { mapState } from 'vuex';
import axios from 'axios';

export default {
  name: "MyProfile",
  data() {
    return {
      username: "",
      email: "",
      password: ""
    };
  },
  computed: {
    ...mapState({
      user: state => state.user
    })
  },
  mounted() {

    if (this.user) {
      this.username = this.user.name;
      this.email = this.user.email;
  }
  },
  methods: {
    async updateProfile() {
  try {
    const payload = {
      name: this.username,
      email: this.email
    };

    if (this.password) {
      payload.password = this.password;
    }

    const token = localStorage.getItem('token');

    const response = await axios.put('/api/user/profile', payload, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    const role = localStorage.getItem('role');

    localStorage.setItem("lastEmail", this.email);

    this.$store.commit('SET_AUTH', {
      token,
      user: response.data.user,
      role
    });

    alert("Profile updated successfully!");
    this.password = "";
  } catch (error) {
    console.error("Profile update failed:", error);

    if (error.response) {
      // Laravel validation or other errors
      console.error("Error response:", error.response.data);
      alert("Failed: " + (error.response.data.message || "Something went wrong"));
    } else if (error.request) {
      // No response received
      console.error("No response received:", error.request);
      alert("Server not responding.");
    } else {
      // Error setting up the request
      console.error("Axios setup error:", error.message);
      alert("Request error: " + error.message);
    }
  }
}

  }


};
</script>
