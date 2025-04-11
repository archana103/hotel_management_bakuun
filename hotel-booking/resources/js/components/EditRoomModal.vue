<template>
    <div v-if="show" class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Room</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitEdit">
              <div class="mb-3">
                <label class="form-label">Room Type</label>
                <input v-model="form.room_type" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Capacity</label>
                <input type="number" v-model="form.capacity" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" v-model="form.price" class="form-control" required />
              </div>
              <button class="btn btn-primary">Update Room</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue'
  import axios from 'axios'
  
  const props = defineProps(['room'])
  const emit = defineEmits(['close', 'updated'])
  
  const show = ref(false)
  const form = ref({
    room_type: '',
    capacity: '',
    price: '',
  })
  
  // Populate form when room is passed
  watch(() => props.room, (newRoom) => {
    if (newRoom) {
      form.value = {
        room_type: newRoom.room_type,
        capacity: newRoom.capacity,
        price: newRoom.price
      }
    }
  })
  
  const openModal = () => {
    show.value = true
  }
  
  const closeModal = () => {
    show.value = false
    emit('close')
  }
  
  const submitEdit = async () => {
    const token = localStorage.getItem('token')
    await axios.put(`/api/rooms/${props.room.id}`, form.value, {
      headers: { Authorization: `Bearer ${token}` }
    })
    emit('updated')
    closeModal()
  }
  
  defineExpose({ openModal })
  </script>
  