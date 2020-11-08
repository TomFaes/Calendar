import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    LoggedInUser: '',
    authenticated: false,
    role: '',
    errorMessage: '',
  },

 
  mutations: {
    setAuthentication(state, status){
      state.authenticated = status;
    },

    setLoggedInUser(state, user){
      state.LoggedInUser = user;
    },
    
    setRole(state, role){
      state.role = role;
    },

    setErrorMessage(state, message){
      state.errorMessage = message;
    }
  },
  actions: {

  },
  getters: {

  }
})