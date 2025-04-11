import { createStore } from 'vuex';
import axios from 'axios';

export default createStore({
    state: {
        token: localStorage.getItem('token') || null,
        user: JSON.parse(localStorage.getItem('user')) || null,
        role: localStorage.getItem('role') || null,
        permissions: JSON.parse(localStorage.getItem('permissions')) || [],  // NEW
        errorMessage: ''
    },
    mutations: {
        SET_AUTH(state, { token, user, role, permissions }) {
            state.token = token;
            state.user = user;
            state.role = role;
            state.permissions = permissions || [];
        
            localStorage.setItem('token', token);
            localStorage.setItem('user', JSON.stringify(user));
            localStorage.setItem('role', role);
            localStorage.setItem('permissions', JSON.stringify(permissions || []));
        
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        },
        
        LOGOUT(state) {
            state.token = null;
            state.user = null;
            state.role = null;
            state.permissions = [];
        
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('role');
            localStorage.removeItem('permissions');
        
            delete axios.defaults.headers.common['Authorization'];
        },
        
        SET_ERROR(state, message) {
            state.errorMessage = message;
        },
        CLEAR_ERROR(state) {
            state.errorMessage = '';
        }
    },
    actions: {
        async login({ commit }, credentials) {
            try {
                const response = await axios.post('/api/login', credentials);
                
                if (!response.data || !response.data.token) {
                    commit('SET_ERROR', 'Invalid login response from server.');
                    return false;
                }

                
                const role = response.data.roles[0];

                commit('SET_AUTH', {
                    token: response.data.token,
                    user: response.data.user,
                    role,
                    permissions: response.data.permissions || []
                  });
                return role; 
            } catch (error) {
                const message = error.response?.data?.message || 'Login failed. Please try again.';
                commit('SET_ERROR', message);
                console.error("Login error:", message);
                return null;
            }
        },
        async register({ commit }, userData) {
            try {
                const response = await axios.post('/api/register', userData);
                
                if (!response.data || !response.data.token) {
                    commit('SET_ERROR', 'Invalid registration response from server.');
                    return false;
                }

                
                const role = response.data.roles[0];

                commit('SET_AUTH', { 
                    user: response.data.user, 
                    token: response.data.token, 
                    role
                });

                return role;
            } catch (error) {
                const message = error.response?.data?.message || 'Registration failed. Please try again.';
                commit('SET_ERROR', message);
                console.error("Registration error:", message);
                return null;
            }
        },
        async logout({ commit }) {
            try {
                // Send logout request *before* clearing token
                await axios.post('/api/logout');
            } catch (error) {
                console.error("Logout error:", error);
            }
        
            // Now clear token
            commit('LOGOUT');
        },
        setError({ commit }, message) {
            commit('SET_ERROR', message);
            setTimeout(() => commit('CLEAR_ERROR'), 5000); // Auto-clear error after 5 seconds
        }
    },
    getters: {
        isAuthenticated: state => !!state.token,
        getUser: state => state.user,
        getRole: state => state.role,
        getPermissions: state => state.permissions,
        hasPermission: state => permission => state.permissions.includes(permission),
        getErrorMessage: state => state.errorMessage
    }
    
});
