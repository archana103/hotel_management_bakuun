<template>
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-sm" style="width: 350px;">
      <h2 class="text-center mb-3 text-primary">Register</h2>

      <!-- Alert Message -->
      <AlertMessage
        v-if="message"
        :message="message"
        :type="messageType"
        :duration="4000"
      />

      <form @submit.prevent="handleRegister">
        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input
            v-model="form.name"
            type="text"
            id="name"
            class="form-control"
            placeholder="Enter your name"
          />
          <div v-if="errors.name" class="text-danger small mt-1">{{ errors.name }}</div>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input
            v-model="form.email"
            type="email"
            id="email"
            class="form-control"
            placeholder="Enter your email"
          />
          <div v-if="errors.email" class="text-danger small mt-1">{{ errors.email }}</div>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input
            v-model="form.password"
            type="password"
            id="password"
            class="form-control"
            placeholder="Enter your password"
          />
          <div v-if="errors.password" class="text-danger small mt-1">{{ errors.password }}</div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>
      </form>

      <div class="text-center mt-3">
        <router-link to="/" class="text-decoration-none">
          Already have an account? Login
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, watch } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
import AlertMessage from '@/components/AlertMessage.vue'; // Make sure path is correct

const store = useStore();
const router = useRouter();

const form = reactive({
  name: '',
  email: '',
  password: '',
  role: 'users'
});

const errors = reactive({
  name: '',
  email: '',
  password: ''
});

const message = ref('');
const messageType = ref('');

// Validation logic
const validateForm = () => {
  errors.name = form.name.trim() === '' ? 'Name is required' : '';
  errors.email = form.email.trim() === '' ? 'Email is required' : '';
  errors.password =
    form.password.length < 6 ? 'Password must be at least 6 characters' : '';
};

watch(form, validateForm, { deep: true, immediate: false });

const handleRegister = async () => {
  validateForm();

  if (errors.name || errors.email || errors.password) {
    message.value = 'Please fill all fields.';
    messageType.value = 'danger';
    return;
  }

  const success = await store.dispatch('register', { ...form });

  if (success) {
    localStorage.setItem('lastEmail', form.email);
    message.value = 'Registered successfully! Redirecting...';
    messageType.value = 'success';
    setTimeout(() => router.push('/'), 2000);
  } else {
    message.value = 'Registration failed. Try again.';
    messageType.value = 'danger';
  }
};
</script>
