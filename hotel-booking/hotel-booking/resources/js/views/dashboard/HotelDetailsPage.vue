<template>


  <div class="container py-4">
    <!-- Hotel Name -->


    <!-- Main Image and Gallery -->
    <!-- Main Image -->
<div class="col-md-12 mb-3">
  <div class="card shadow-sm" v-if="hotel.image">
    <img :src="getImageUrl(hotel.image)" class="card-img-top" alt="Main hotel image"
      style="height: 200px; object-fit: cover;" />
    <div class="card-body p-2 text-center">
      <p class="card-text text-muted small mb-2">Main Image</p>
     
    </div>
  </div>
  <input type="file" @change="uploadMainImage" accept="image/*" />
</div>

<!-- Additional Images -->
<div class="d-flex flex-wrap">
  <template v-if="hotel.images && hotel.images.length > 0">
    <h5 class="w-100">Gallery</h5>
    <div v-for="img in hotel.images" :key="img.id" class="position-relative me-2 mb-2">
      <img :src="getImageUrl(img.image_path)" alt="Hotel image"
        class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;" />
      <button class="btn btn-sm btn-danger position-absolute top-0 end-0"
        @click="deleteGalleryImage(img.id)">×</button>
    </div>
  </template>
  <p v-else class="text-muted">No images available.</p>
</div>

<!-- Upload new gallery images -->
<input type="file" multiple @change="uploadGalleryImages" accept="image/*" class="mt-2" />

    <div class="d-flex align-items-center justify-content-center mb-3">
      <template v-if="editingName">
        <input v-model="editableName" class="form-control me-2" style="max-width: 300px;" />
        <button @click="updateHotelField('name', editableName)" class="btn btn-success btn-sm me-2">
          <i class="bi bi-check-lg"></i>
        </button>
        <button @click="editingName = false" class="btn btn-secondary btn-sm">
          <i class="bi bi-x-lg"></i>
        </button>
      </template>
      <template v-else>
        <h2 class="mb-0 me-2">{{ hotel.name }}</h2>
        <button @click="startEditingName" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-pencil"></i>
        </button>
      </template>
    </div>

    <!-- Description -->
    <div class="mb-3">
      <label class="form-label fw-bold">Description:</label>
      <div class="d-flex align-items-start">
        <template v-if="editingDescription">
          <textarea v-model="editableDescription" class="form-control me-2" rows="3"
            style="max-width: 500px;"></textarea>
          <div class="mt-1">
            <button @click="updateHotelField('description', editableDescription)" class="btn btn-success btn-sm me-2">
              <i class="bi bi-check-lg"></i>
            </button>
            <button @click="editingDescription = false" class="btn btn-secondary btn-sm">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </template>
        <template v-else>
          <p class="me-2" style="font-size: larger;">{{ hotel.description }}</p>
          <button @click="startEditingDescription" class="btn btn-outline-primary btn-sm mt-1">
            <i class="bi bi-pencil"></i>
          </button>
        </template>
      </div>
    </div>

    <!-- Location & Address -->
    <p>
      <strong>Address:</strong>
      <template v-if="editingAddress">
        <textarea v-model="hotel.address" class="form-control mb-1"></textarea>
        <i class="bi bi-check-circle text-success me-2 cursor-pointer"
          @click="updateHotelField('address', hotel.address)"></i>
      </template>
      <template v-else>
        {{ hotel.address }}
        <i class="bi bi-pencil-square text-primary ms-2 cursor-pointer" @click="editingAddress = true"></i>
      </template>
    </p>
    <p>
      <strong>Location:</strong>
      <template v-if="editingLocation">
        <select v-model="hotel.location" class="form-select w-auto d-inline-block me-2">
          <option v-for="loc in countries" :key="loc" :value="loc">{{ loc }}</option>
        </select>
        <i class="bi bi-check-circle text-success cursor-pointer"
          @click="updateHotelField('location', hotel.location)"></i>
      </template>
      <template v-else>
        {{ hotel.location }}
        <i class="bi bi-pencil-square text-primary ms-2 cursor-pointer" @click="editingLocation = true"></i>
      </template>
    </p>


    <!-- Amenities -->
    <div class="mb-4">
      <div>
        <h5 class="d-inline">Amenities</h5>
        <i v-if="!editingAmenities" class="bi bi-pencil-square text-primary ms-2 cursor-pointer"
          @click="editingAmenities = true"></i>
      </div>

      <!-- Editing Mode -->
      <div v-if="editingAmenities">
        <select v-model="selectedAmenities" multiple class="form-select mb-2">
          <option v-for="a in availableAmenities.filter(a => !(hotel.value?.amenities?.some(ha => ha.id === a.id)))"
            :key="a.id" :value="a.id">
            {{ a.name }}
          </option>
        </select>


        <button class="btn btn-sm btn-success" @click="updateHotelAmenities">Save</button>
      </div>

      <!-- Display Amenities -->
      <div class="mt-2">
        <template v-if="hotel.amenities && hotel.amenities.length > 0">
          <span v-for="a in hotel.amenities" :key="a.id" class="badge bg-info text-dark me-2 mb-1">
            {{ a.name }}
            <i v-if="editingAmenities" class="bi bi-x ms-1 text-danger cursor-pointer" @click="removeAmenity(a.id)"></i>
          </span>
        </template>
        <p v-else class="text-muted">No amenities available.</p>
      </div>
    </div>
    <div class="mb-4">
  <div>
    <h5 class="d-inline">Managers</h5>
    <ul>
      <li v-for="m in hotel.managers" :key="m.id">
        {{ m.name }} - {{ m.email }}
      </li>
    </ul>
  </div>
  
</div>



    <hr />

    <h4>Add Room</h4>
    <form @submit.prevent="addRoom">
      <div class="mb-2">
        <label class="form-label">Room Type</label>
        <input type="text" v-model="room.room_type" class="form-control" required />
      </div>
      <div class="mb-2">
        <label class="form-label">Capacity</label>
        <input type="number" v-model="room.capacity" class="form-control" required />
      </div>
      <div class="mb-2">
        <label class="form-label">Price</label>
        <input type="number" v-model="room.price" class="form-control" required />
      </div>
      <button class="btn btn-outline-success mt-2" type="submit">Add Room</button>
    </form>

    <hr />

    <h4 class="mt-4">Rooms</h4>
    <table class="table table-bordered mt-2">
      <thead>
        <tr>
          <th>ID</th>
          <th>Type</th>
          <th>Capacity</th>
          <th>Price</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="room in rooms" :key="room.id">
          <td>{{ room.id }}</td>
          <td>{{ room.room_type }}</td>
          <td>{{ room.capacity }}</td>
          <td>{{ room.price }}</td>
          <td>
            <span class="badge rounded-pill" style="font-size: medium;"
              :class="room.current_status === 'available' ? 'bg-success' : 'bg-danger'">
              {{ room.current_status }} For today
            </span>
          </td>
          <td>
            <button class="btn btn-outline-primary btn-sm me-1" @click="openEditModal(room)">Edit</button>
            <button class="btn btn-sm btn-outline-success me-1" @click="openBookModal(room)">Book</button>
            <button class="btn btn-sm btn-outline-info" @click="openHistoryModal(room)">History</button>
          </td>
        </tr>
      </tbody>

    </table>
  </div>
  <EditRoomModal ref="editRoomModal" :room="selectedRoom" @updated="fetchRooms" @close="selectedRoom = null" />

  <BookRoomModal v-if="showBookModal" :room-id="selectedRoom?.id" :show="showBookModal" @close="showBookModal = false"
    @booked="fetchRooms" />
  <!-- Booking History Modal -->
  <BookingHistoryModal v-if="showHistoryModal" :room="selectedRoom" @close="showHistoryModal = false"
    @updated="fetchRooms" />

</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import axios from 'axios'
import { useRoute } from 'vue-router'

import EditRoomModal from '@/components/EditRoomModal.vue'
import BookRoomModal from '@/components/BookRoomModal.vue'
import BookingHistoryModal from '@/components/BookingHistoryModal.vue'

const route = useRoute()
const hotelId = route.params.id

const showEditModal = ref(false)
const showBookModal = ref(false)
const showHistoryModal = ref(false)
const editingAddress = ref(false)
const editingLocation = ref(false)
const editRoomModal = ref(null)
const selectedRoom = ref(null)
const editingName = ref(false)
const editableName = ref('')
const editingDescription = ref(false)
const editableDescription = ref('')
const hotel = ref({})
const rooms = ref([])
const editingAmenities = ref(false)
const availableAmenities = ref([])
const selectedAmenities = ref([])
const room = ref({
  room_type: '',
  capacity: '',
  price: '',
})

const token = localStorage.getItem('token')

const openEditModal = (room) => {
  selectedRoom.value = room
  nextTick(() => {
    editRoomModal.value?.openModal()
  })
}

const openBookModal = (room) => {
  selectedRoom.value = room
  showBookModal.value = true
}

const openHistoryModal = (room) => {
  selectedRoom.value = room
  showHistoryModal.value = true
}

const getImageUrl = (path) => `/storage/${path}`

const fetchHotel = async () => {
  const res = await axios.get(`/api/hotels/${hotelId}`, {
    headers: {
      Authorization: `Bearer ${token}`
    }
  })
  hotel.value = res.data
}
const startEditingName = () => {
  editableName.value = hotel.value.name
  editingName.value = true
}
const startEditingDescription = () => {
  editableDescription.value = hotel.value.description
  editingDescription.value = true
}
const updateHotelField = async (field, value) => {
  try {
    await axios.put(`/api/hotels/${hotelId}`, {
      [field]: value,
    }, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })

    hotel.value[field] = value

    if (field === 'name') editingName.value = false
    else if (field === 'description') editingDescription.value = false
    else if (field === 'address') editingAddress.value = false
    else if (field === 'location') editingLocation.value = false

  } catch (error) {
    console.error('Failed to update hotel field:', error)
  }
}
const updateHotelAmenities = async () => {
  try {


    await axios.put(`/api/hotels/${hotelId}`, {
      amenities: selectedAmenities.value, // use this instead
    }, {
      headers: { Authorization: `Bearer ${token}` },
    })

    // Replace current hotel amenities with newly selected ones
    hotel.value.amenities = availableAmenities.value.filter(a =>
      selectedAmenities.value.includes(a.id)
    )

    editingAmenities.value = false
  } catch (error) {
    console.error('Failed to update amenities:', error)
  }
}

const uploadMainImage = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('main_image', file)

  try {
    await axios.post(`/api/hotels/${hotelId}/main-image`, formData, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'multipart/form-data'
      }
    })
    await fetchHotel()
  } catch (err) {
    console.error('Main image upload failed:', err)
  }
}

const uploadGalleryImages = async (event) => {
  const files = event.target.files
  if (!files.length) return

  const formData = new FormData()
  for (const file of files) {
    formData.append('images[]', file)
  }

  try {
    await axios.post(`/api/hotels/${hotelId}/images`, formData, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'multipart/form-data'
      }
    })
    await fetchHotel()
  } catch (err) {
    console.error('Gallery image upload failed:', err)
  }
}

const deleteGalleryImage = async (imageId) => {
  try {
    await axios.delete(`/api/hotel-images/${imageId}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    await fetchHotel()
  } catch (err) {
    console.error('Image deletion failed:', err)
  }
}

const fetchAvailableAmenities = async () => {
  try {
    const res = await axios.get('/api/amenities', {
      headers: { Authorization: `Bearer ${token}` },
    })

    availableAmenities.value = res.data
    console.log(availableAmenities.value);
  } catch (err) {
    console.error('Failed to load amenities:', err)
  }
}
const fetchRooms = async () => {
  try {
    const res = await axios.get(`/api/hotels/${hotelId}/rooms`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    const roomList = res.data

    // Attach availability status to each room
    for (const room of roomList) {
      const availability = await axios.get(`/api/rooms/${room.id}/availability`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      room.current_status = availability.data.status
    }

    rooms.value = roomList
  } catch (err) {
    console.error('Failed to fetch rooms or availability:', err)
  }
}

const addRoom = async () => {
  await axios.post('/api/rooms', {
    hotel_id: hotelId,
    room_type: room.value.room_type,
    capacity: room.value.capacity,
    price: room.value.price,
  }, {
    headers: {
      Authorization: `Bearer ${token}`
    }
  })

  room.value = { room_type: '', capacity: '', price: '' }
  await fetchRooms()
}
const countries = ref([
  'United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France',
  'India', 'China', 'Japan', 'Brazil', 'South Africa', 'Italy', 'Spain', 'Mexico',
  'Russia', 'Netherlands', 'Sweden', 'Switzerland', 'New Zealand', 'Singapore'
])
onMounted(async () => {
  await fetchHotel()
  await fetchRooms()
  await fetchAvailableAmenities()
})
</script>

<style scoped>
.card-img-top {
  object-fit: cover;
  height: 200px;
  border-radius: 0.5rem;
}
</style>