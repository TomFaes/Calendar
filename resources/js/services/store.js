import { createStore } from 'vuex'

var localPath = "";
if(process.env.NODE_ENV == 'development'){
    localPath = process.env.MIX_APP_URL;
}

export default new createStore({
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
    message: '',
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
    },

    setMessage(state, message){
      state.message = message;
    },
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

    getSelectedGroup({commit, dispatch}, {id}) {
      axios.get( localPath +  '/api/group/' + id)
        .then((response) => {
          if(response.status == 203){
            dispatch('getMessage', {message: response.data, color: 'red'});
          } 
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

    getSelectedSeason({commit, dispatch}, {id}){
      axios.get( localPath +  '/api/season/' + id)
        .then((response) => {
          if(response.status == 203){
            dispatch('getMessage', {message: response.data, color: 'red'});
          } 
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

    getSelectedPublicCalendar({commit}, {id}){
      axios.get( localPath +  '/api/season/' + id + '/public')
        .then((response) => {
          commit('setSelectedCalendar', response);
        })
        .catch((error) => console.error(error));
    },

    getMessage({commit}, {message, color = 'green', time = 5000}){
      var response = {};
      response['message'] = message;
      response['color'] = color;
      response['time'] = time;
      commit('setMessage', response); 
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