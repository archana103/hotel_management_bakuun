<template>
    <div>
      <router-view></router-view> 
      <!-- component tells Vue to display the component that matches the current route -->
    </div>
  </template>
  
  <script>
  export default {
    name: "App",
  
    mounted() {
      window.addEventListener("storage", this.handleStorageEvent);
    },
  
    beforeUnmount() {
      window.removeEventListener("storage", this.handleStorageEvent);
    },
  
    methods: {
      handleStorageEvent(event) {
        if (event.key === "token" && event.newValue === null) {
   
          this.$router.push("/");
        }
      }
    }
  };
  </script>
  
  <!-- this is root component here what ever component we will ad will mount in blade .This is the entry point for Vue Router, meaning different pages/components will be loaded dynamically -->