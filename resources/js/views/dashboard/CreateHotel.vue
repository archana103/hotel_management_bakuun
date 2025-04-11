<template>
    <div class="container mt-4">
        <h2>Add New Hotel</h2>
        <form @submit.prevent="submitHotel">
            <div class="mb-3">
                <label class="form-label">Hotel Name</label>
                <input type="text" v-model="hotel.name" class="form-control" required />
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea v-model="hotel.description" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Country</label>
                <select v-model="hotel.location" class="form-select" required>
                    <option disabled value="">Select Country</option>
                    <option v-for="country in countries" :key="country" :value="country">
                        {{ country }}
                    </option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" v-model="hotel.address" class="form-control" />
            </div>

            <div class="mb-3">
                <label class="form-label">Main Image</label>
                <input type="file" @change="handleMainImage" class="form-control" />
            </div>

            <div class="mb-3">
                <label class="form-label">Additional Images</label>
                <input type="file" multiple @change="handleHotelImages" class="form-control" />
            </div>

            <div class="mb-3">
                <label class="form-label">Amenities</label>
                <select multiple v-model="hotel.amenities" class="form-select">
                    <option v-for="a in amenities" :key="a.id" :value="a.id">{{ a.name }}</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Assign Manager(s)</label>
                <select multiple v-model="hotel.managers" class="form-select">
                    <option v-for="manager in managers" :key="manager.id" :value="manager.id">{{ manager.name }}
                    </option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Create Hotel</button>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router';
const router = useRouter();
const hotel = ref({
    name: '',
    description: '',
    location: '',
    address: '',
    image: null,
    hotel_images: [],
    amenities: [],
    managers: []
})

const amenities = ref([])
const managers = ref([])

const handleMainImage = (e) => {
    hotel.value.image = e.target.files[0]
}

const handleHotelImages = (e) => {
    hotel.value.hotel_images = Array.from(e.target.files)
}

const fetchData = async () => {
    const [amenityRes, managerRes] = await Promise.all([
        axios.get('/api/amenities'),
        axios.get('/api/users?role=admin')
    ])
    amenities.value = amenityRes.data
    managers.value = managerRes.data
}

const submitHotel = async () => {
    const formData = new FormData()
    formData.append('name', hotel.value.name)
    formData.append('description', hotel.value.description)
    formData.append('location', hotel.value.location)
    formData.append('address', hotel.value.address)
    formData.append('image', hotel.value.image)

    hotel.value.hotel_images.forEach((file, i) => {
        formData.append(`hotel_images[${i}]`, file)
    })
console.log(hotel.value.address);
    hotel.value.amenities.forEach(id => formData.append('amenities[]', id))
    hotel.value.managers.forEach(id => formData.append('managers[]', id))

    const token = localStorage.getItem('token')

    await axios.post('/api/hotels', formData, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'multipart/form-data'
        }
    });
    alert('Hotel created successfully!')
    router.push('/dashboard/hotels');
}
const countries = ref([
    'United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France',
    'India', 'China', 'Japan', 'Brazil', 'South Africa', 'Italy', 'Spain', 'Mexico',
    'Russia', 'Netherlands', 'Sweden', 'Switzerland', 'New Zealand', 'Singapore'
])
onMounted(fetchData)
</script>