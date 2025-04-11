<template>
  <div class="container">
    <h2 class="mb-4 fw-bold textcolor"> <i class="bi bi-person-circle me-2"></i>My Profile</h2>

    <div class="d-flex justify-content-center align-items-center">
      <div class="card p-5 shadow-sm" style="max-width:900px; width: 100%;">
        
        <form @submit.prevent="updateProfile">
          <div class="mb-3">
            <label for="username" class="form-label">User Name</label>
            <input type="text" class="form-control" id="username" v-model="username" required />
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email Id</label>
            <input type="email" class="form-control" id="email" v-model="email" required />
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" v-model="password" id="password"
              placeholder="Update Your Password" />
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-outline-success"  style="max-width:200px; width: 100%;">
              Update
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import axios from 'axios'

const store = useStore()

const username = ref('')
const email = ref('')
const password = ref('')

const user = computed(() => store.state.user)

onMounted(() => {
  if (user.value) {
    username.value = user.value.name
    email.value = user.value.email
  }
})

const updateProfile = async () => {
  try {
    const payload = {
      name: username.value,
      email: email.value
    }

    if (password.value) {
      payload.password = password.value
    }

    const token = localStorage.getItem('token')

    const response = await axios.put('/api/user/profile', payload, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    const role = localStorage.getItem('role')
    localStorage.setItem('lastEmail', email.value)

    store.commit('SET_AUTH', {
      token,
      user: response.data.user,
      role
    })

    alert('Profile updated successfully!')
    password.value = ''
  } catch (error) {
    console.error('Profile update failed:', error)

    if (error.response) {
      alert('Failed: ' + (error.response.data.message || 'Something went wrong'))
    } else if (error.request) {
      alert('Server not responding.')
    } else {
      alert('Request error: ' + error.message)
    }
  }
}
</script>

