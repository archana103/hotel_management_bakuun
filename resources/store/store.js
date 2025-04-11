import { createStore } from 'vuex';

export default createStore({
  state: {
    errorMessage: ''
  },
  mutations: {
    SET_ERROR_MESSAGE(state, message) {
      state.errorMessage = message;
    },
    CLEAR_ERROR_MESSAGE(state) {
      state.errorMessage = '';
    }
  },
  actions: {
    setError({ commit }, message) {
      commit('SET_ERROR_MESSAGE', message);
      setTimeout(() => commit('CLEAR_ERROR_MESSAGE'), 5000); // Auto clear after 5 sec
    }
  }
});
