<template>
  <div>
    <!-- Header & Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Manage Admins</h2>
      <button class="btn btn-outline-primary" @click="showModal = true">
        Register Admin
      </button>
    </div>

    <!-- Admins Table -->
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="admin in admins" :key="admin.id">
          <td>{{ admin.name }}</td>
          <td>{{ admin.email }}</td>
          <td>
            <button class="btn btn-sm btn-outline-warning me-2" @click="editAdmin(admin)">Edit</button>
            <button class="btn btn-sm btn-outline-danger" @click="deleteAdmin(admin.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3" v-if="pagination.last_page > 1">
      <nav>
        <ul class="pagination">
          <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
            <a class="page-link" href="#" @click.prevent="fetchAdmins(pagination.current_page - 1)">Previous</a>
          </li>
          <li
            class="page-item"
            v-for="page in pagination.last_page"
            :key="page"
            :class="{ active: page === pagination.current_page }"
          >
            <a class="page-link" href="#" @click.prevent="fetchAdmins(page)">
              {{ page }}
            </a>
          </li>
          <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
            <a class="page-link" href="#" @click.prevent="fetchAdmins(pagination.current_page + 1)">Next</a>
          </li>
        </ul>
      </nav>
    </div>

    <!-- Register Modal -->
    <div v-if="showModal" class="modal-backdrop">
      <div class="modal-content p-4">
        <h4>Register New Admin</h4>
        <form @submit.prevent="registerAdmin">
          <div class="mb-2">
            <input v-model="form.name" type="text" class="form-control" placeholder="Name" required />
          </div>
          <div class="mb-2">
            <input v-model="form.email" type="email" class="form-control" placeholder="Email" required />
          </div>
          <div class="mb-2">
            <input v-model="form.password" type="password" class="form-control" placeholder="Password" required />
          </div>
          <div class="text-end">
            <button class="btn btn-outline-secondary me-2" @click="showModal = false">Cancel</button>
            <button type="submit" class="btn btn-outline-success">Register</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="showEditModal" class="modal-backdrop">
      <div class="modal-content p-4">
        <h4>Edit Admin</h4>
        <form @submit.prevent="updateAdmin">
          <div class="mb-2">
            <input v-model="editForm.name" type="text" class="form-control" placeholder="Name" required />
          </div>
          <div class="mb-2">
            <input v-model="editForm.email" type="email" class="form-control" placeholder="Email" required />
          </div>
          <div class="text-end">
            <button class="btn btn-outline-secondary me-2" @click="showEditModal = false">Cancel</button>
            <button type="submit" class="btn btn-outline-success">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const admins = ref([])
const pagination = ref({})
const showModal = ref(false)
const showEditModal = ref(false)

const form = ref({
  name: '',
  email: '',
  password: '',
  role: 'admin',
})

const editForm = ref({
  id: null,
  name: '',
  email: ''
})

const fetchAdmins = async (page = 1) => {
  try {
    const token = localStorage.getItem('token')
    const res = await axios.get(`/api/admins?page=${page}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    admins.value = res.data.data
    pagination.value = {
      current_page: res.data.current_page,
      last_page: res.data.last_page
    }
  } catch (err) {
    console.error('Failed to fetch admins:', err.response?.data || err)
  }
}

const registerAdmin = async () => {
  try {
    const token = localStorage.getItem('token')
    await axios.post('/api/register-admin', form.value, {
      headers: { Authorization: `Bearer ${token}` }
    })
    form.value = { name: '', email: '', password: '', role: 'admin' }
    showModal.value = false
    fetchAdmins()
  } catch (err) {
    console.error('Registration failed:', err.response?.data || err)
  }
}

const editAdmin = (admin) => {
  editForm.value = { id: admin.id, name: admin.name, email: admin.email }
  showEditModal.value = true
}

const updateAdmin = async () => {
  try {
    const token = localStorage.getItem('token')
    const { id, name, email } = editForm.value
    await axios.put(`/api/admins/${id}`, { name, email }, {
      headers: { Authorization: `Bearer ${token}` }
    })
    showEditModal.value = false
    fetchAdmins(pagination.value.current_page)
  } catch (err) {
    console.error('Update failed:', err.response?.data || err)
  }
}

const deleteAdmin = async (id) => {
  if (!confirm('Are you sure you want to delete this admin?')) return
  try {
    const token = localStorage.getItem('token')
    await axios.delete(`/api/admins/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    fetchAdmins(pagination.value.current_page)
  } catch (err) {
    console.error('Delete failed:', err.response?.data || err)
  }
}

onMounted(() => fetchAdmins())
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal-content {
  background: white;
  border-radius: 10px;
  width: 400px;
}
</style>
