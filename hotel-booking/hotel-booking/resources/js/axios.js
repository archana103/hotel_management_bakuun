// import axios from 'axios';
// import router from './router'; // adjust path if needed

// axios.defaults.baseURL = 'http://127.0.0.1:8000/'; // optional
// axios.defaults.withCredentials = true; // only if using Sanctum

// // Request Interceptor — attach token
// axios.interceptors.request.use(
//   config => {
//     const token = localStorage.getItem('token');
//     if (token) {
//       config.headers.Authorization = `Bearer ${token}`;
//     }
//     return config;
//   },
//   error => Promise.reject(error)
// );

// // Response Interceptor — handle 402
// axios.interceptors.response.use(
//   response => response,
//   error => {
//     if (error.response && error.response.status === 402) {
//       localStorage.removeItem('token');
//       localStorage.setItem('logout', Date.now()); // trigger logout in other tabs
//       router.push('/');
//     }
//     return Promise.reject(error);
//   }
// );

// export default axios;
