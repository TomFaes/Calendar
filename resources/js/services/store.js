import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

var localPath = "";
if(process.env.NODE_ENV == 'development'){
    localPath= "/tenniscalendar/public_html"
}

export default new Vuex.Store({
  state: {
    loggedInUser: '',
    userGroups: {},
    selectedGroup: {},
    selectedGroupUsers: {},
    userSeasons: {}, 
    selectedSeason: {},
    selectedCalendar: {},

    authenticated: false,
    role: '',
    errorMessage: '',
  },

 
  mutations: {
    setAuthentication(state, status){
      state.authenticated = status;
    },

    setLoggedInUser(state, user){
      state.loggedInUser = user;
    },
    
    setRole(state, role){
      state.role = role;
    },

    setErrorMessage(state, message){
      state.errorMessage = message;
    },

    setUserGroups(state, user){
      state.userGroups = user;
    },

    setSelectedGroup(state, selectedGroup){
      state.selectedGroup = selectedGroup;
    },

    setSelectedGroupUsers(state, user){
      state.selectedGroupUsers = user;
    },

    setUserSeasons(state, season){
      state.userSeasons = season;
    },

    setSelectedSeason(state, season){
      state.selectedSeason = season;
    },

    setSelectedCalendar(state, calendar){
      state.selectedCalendar = calendar;
    }







  },

  actions: {
    getLoggedInUser({commit}){
      axios.get( localPath +  '/api/profile')
      .then((response) => {
        commit('setLoggedInUser', response.data);
      })
      .catch((error) => console.error(error));
    },

    getUserGroups({commit}){
      axios.get( localPath +  '/api/user-group')
      .then((response) => {
        commit('setUserGroups', response.data);
      })
      .catch((error) => console.error(error));
    },

    getSelectedGroup({commit}, {id}) {
      axios.get( localPath +  '/api/group/' + id)
        .then((response) => {
          commit('setSelectedGroup', response.data);
        })
        .catch((error) => console.error(error));
    },

    getSelectedGroupUsers({commit}, {groupId}){
      axios.get( localPath +  '/api/group/' + groupId + '/users')
        .then((response) => {
          commit('setSelectedGroupUsers', response.data);
        })
        .catch((error) => console.error(error));
    },

    getUserSeasons({commit}){
      axios.get( localPath +  '/api/season')
      .then((response) => {
        commit('setUserSeasons', response.data);
      })
      .catch((error) => console.error(error));
    },

    getSelectedSeason({commit}, {id}){
      axios.get( localPath +  '/api/season/' + id)
        .then((response) => {
          commit('setSelectedSeason', response.data);
        })
        .catch((error) => console.error(error));
    },

    getSelectedCalendar({commit}, {id}){
      axios.get( localPath +  '/api/season/' + id + '/generator')
        .then((response) => {
          commit('setSelectedCalendar', response);
        })
        .catch((error) => console.error(error));
    },

    resetToDefault({commit}){
      commit('setSelectedGroup', {});
      commit('setSelectedGroupUsers', {});
      commit('setUserSeasons', {});
      commit('setSelectedSeason', {});
      commit('setSelectedCalendar', {});
    },

    


  

  },
  getters: {

  }
})