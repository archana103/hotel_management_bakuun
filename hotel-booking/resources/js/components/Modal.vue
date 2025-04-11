<!-- resources/js/components/Modal.vue -->
<template>
    <div v-if="show" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ title }}</h5>
            <button type="button" class="btn-close" @click="$emit('close')"></button>
          </div>
          <div class="modal-body">
            <slot></slot>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    show: Boolean,
    title: String
  })
  </script>
  