<template>
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-sm" style="width: 350px;">
      <h2 class="text-center mb-3 text-primary">Login</h2>

      <AlertMessage v-if="errorMessage" :message="errorMessage" type="danger" :duration="4000" />

      <form @submit.prevent="handleLogin">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input v-model="form.email" type="email" id="email" class="form-control" placeholder="Enter your email"
            required />
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input v-model="form.password" type="password" id="password" class="form-control"
            placeholder="Enter your password" required />
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>

      <div class="text-center mt-3">
        <router-link to="/register" class="text-decoration-none">Don't have an account? Register</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
import axios from 'axios';
import AlertMessage from '../components/AlertMessage.vue';

const store = useStore();
const router = useRouter();

const form = reactive({
  email: localStorage.getItem("lastEmail") || '',
  password: ''
});

const errorMessage = computed(() => store.getters.getErrorMessage);

const handleLogin = async () => {
  try {
    await axios.get('/sanctum/csrf-cookie');

    const role = await store.dispatch('login', {
      email: form.email,
      password: form.password
    });

    if (role) {
      localStorage.setItem('lastEmail', form.email);
      if (role === 'admin' || role === 'master') {
        router.push('/dashboard/overview');
      } else {
        router.push('/user/dashboard/search-hotel'); // or wherever for normal users
      }
    }
  } catch (err) {
    console.error('Login failed:', err);
  }
};
</script>
