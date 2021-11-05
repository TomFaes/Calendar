import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../services/store';
import axios from 'axios';

Vue.use(VueRouter);

//All components that will be used in the router
import Home from '../pages/IndexPage/main.vue';
import Login from '../pages/IndexPage/login.vue';

import Profile from '../pages/ProfilePage/index.vue';
import JoinGroup from '../pages/GroupUserPage/joinGroup.vue';

import Groups from '../pages/GroupPage/index.vue';
import GroupDetail from '../pages/GroupPage/details.vue';
import EditGroup from '../pages/GroupPage/input.vue';
import GroupUsers from '../pages/GroupUserPage/list.vue';

import Seasons from '../pages/SeasonPage/index.vue';
import SeasonDetail from '../pages/SeasonPage/details.vue';
import EditSeason from '../pages/SeasonPage/input.vue';
import Absence from '../pages/AbsencePage/absence.vue';
import Calendar from '../pages/CalendarPage/index.vue';
import DayCalendar from '../pages/NextPlayDayPage/index.vue';
import Generate from '../pages/CalendarPage/generate.vue';





//create a variable local path, in production there will be antohter path
var localPath = "";
if(process.env.NODE_ENV == 'development'){
    localPath= "/tenniscalendar/public_html"
}

const router = new VueRouter({
    //export default new VueRouter({
    mode: 'history', 
    base: process.env.BASE_URL,
    linkActiveClass: 'active',
    transitionOnLoad: true,
    history: true,
    routes:[
        {
            path: localPath + '/',
            name: 'home',
            component: Home,
            meta: { 
                requiresAuth: true,
            }
        },
        {
            path: localPath + '/guest',
            name: 'guest',
            component: Home,
        },

        {
            path: localPath + '/login',
            name: 'login',
            component: Login,
        },

        {
            path: localPath + '/profile',
            name: 'profile',
            component: Profile, 
            meta: { 
                requiresAuth: true,
            }
        },
        
        {
            path: localPath + '/group',
            name: 'group',
            component: Groups, 
            meta: { 
                requiresAuth: true,
            }
        },

        {
            path: localPath + '/group/:id',
            name: 'groupDetail',
            component: GroupDetail,
            props: true,
            meta: {
                requiresAuth: true
            },
            children: [
                {
                    name: 'editGroup',
                    path: 'edit',
                    props: true,
                    components: {
                        groupDetails: EditGroup
                    },
                    meta: {
                        requiresAuth: true
                    },
                },

                {
                    name: 'groupUsers',
                    path: 'group_users',
                    props: true,
                    components: {
                        groupDetails: GroupUsers
                    },
                    meta: {
                        requiresAuth: true
                    },
                },
            ]
        },

        {
            path: localPath + '/join_group',
            name: 'joinGroup',
            component: JoinGroup,
            meta: {
                requiresAuth: true,
            },
        },

        {
            path: localPath + '/season',
            name: 'season',
            component: Seasons, 
            meta: { 
                requiresAuth: true,
            }
        },

        
        {
            path: localPath + '/season/:id',
            name: 'seasonDetail',
            component: SeasonDetail,
            props: true,
            meta: {
                requiresAuth: true
            },

            children: [
                {
                    name: 'editSeason',
                    path: 'edit',
                    props: true,
                    components: {
                        seasonDetails: EditSeason
                    },
                    meta: {
                        requiresAuth: true
                    },
                },

                {
                    name: 'absence',
                    path: 'absence',
                    props: true,
                    components: {
                        seasonDetails: Absence
                    },
                    meta: {
                        requiresAuth: true
                    },
                },

                {
                    name: 'calendar',
                    path: 'calendar',
                    props: true,
                    components: {
                        seasonDetails: Calendar
                    },
                    meta: {
                        requiresAuth: true
                    },
                },

                {
                    name: 'dayCalendar',
                    path: 'day_calendar',
                    props: true,
                    components: {
                        seasonDetails: DayCalendar
                    },
                    meta: {
                        requiresAuth: true
                    },
                },

                {
                    name: 'generate',
                    path: 'generate',
                    props: true,
                    components: {
                        seasonDetails: Generate
                    },
                    meta: {
                        requiresAuth: true
                    },
                },

                /*
                //Make a public calendar link
                {
                    name: 'calendar',
                    path: 'calendar',
                    props: true,
                    components: {
                        seasonDetails: calendar
                    },
                    meta: {
                        requiresAuth: true //!!! not needed because there are public seasons
                    },
                },
                */
 /*
                {
                    name: 'groupUsers',
                    path: 'group_users',
                    props: true,
                    components: {
                        groupDetails: groupUsers
                    }
                },
                */
            ]
            
        },






        /*
        {
            path: localPath + '/*',
            name: 'PageNotFound',
            component: {
                template: '<p>Page Not Found</p>'
            }
        }
        */
    ]
});

router.beforeEach((to, from, next) => {    
    //check if authentication is needen for the route
    if(to.meta.requiresAuth == true){
        //get the user: from the database or from the store
        let getUser = new Promise((resolve, reject) => {
            if(store.state.loggedInUser == ""){
                
                var user = axios({
                    method: 'get',
                    url : localPath + '/api/profile'
                })
                .then(function (response) {
                    //console.log(response);
                    store.commit("setAuthentication", true);
                    store.commit("setRole", response.data.role);
                    store.commit("setLoggedInUser", response.data);
                    return response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
            }else{
               var user = store.state.loggedInUser;
            }
            resolve(user)  // Yay! Everything went well!
        }, 250) ;
       
        getUser.then((user) =>{
            if(user == undefined){
                next({ name: 'guest'});
            }
            if(to.meta.requiresRole != undefined && to.meta.requiresRole != ""){
                if(to.meta.requiresRole == "Admin" && to.meta.requiresRole  == user.role){
                    next();
                }else if(to.meta.requiresRole == "User" && (user.role == 'User' || user.role == "Admin" ) ){
                    next();
                }else{
                    store.commit("setErrorMessage", "Je hebt geen toegang tot deze pagina");
                    next({ name: 'guest'});
                }
            } else {
                next();
            }

        });
    }else{
         next();
    }
   
});
export default router;





