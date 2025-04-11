//this file work as connecting between blade and vue 
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import { createApp } from 'vue';//import vue
import App from './App.vue';//import the root vue component
import router from './router'; // Import the router
import store from '../store';
import 'bootstrap-icons/font/bootstrap-icons.css'
// import axios from './axios'; 
const app = createApp(App);//create a new vue app;ication
app.use(router);//register vue router for handling navigation
app.use(store);
// app.config.globalProperties.$axios = axios;
app.mount('#app');//handles app id in blade

