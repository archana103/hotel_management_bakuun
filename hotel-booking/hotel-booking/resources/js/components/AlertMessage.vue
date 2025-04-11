<template>
    <div
      v-if="visible"
      :class="['alert', `alert-${type}`, 'alert-dismissible', 'fade', 'show']"
      role="alert"
    >
      {{ message }}
    </div>
  </template>
  
  <script setup>
  import { ref, watch, onMounted } from 'vue';
  import { defineProps } from 'vue';
  
  const props = defineProps({
    message: {
      type: String,
      required: true
    },
    type: {
      type: String,
      default: 'success' // 'danger', 'warning', etc.
    },
    duration: {
      type: Number,
      default: 2000 // milliseconds
    }
  });
  
  const visible = ref(true);
  
  onMounted(() => {
    setTimeout(() => {
      visible.value = false;
    }, props.duration);
  });
  </script>
  