import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../services/store';
import axios from 'axios';

Vue.use(VueRouter);

//All components that will be used in the router
import Home from '../pages/nextPlayDayPage/index.vue';
import Login from '../pages/IndexPage/login.vue';
import Profile from '../pages/ProfilePage/index.vue';
import User from '../pages/UserPage/index.vue';
import Group from '../pages/GroupPage/index.vue';
import Season from '../pages/SeasonPage/index.vue';
import Calendar from '../pages/CalendarPage/index.vue';
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
            path: localPath + '/user',
            name: 'user',
            component: User,
            meta: { 
                requiresAuth: true,
                requiresRole: 'Admin',
            }
        },
        {
            path: localPath + '/group',
            name: 'group',
            component: Group,
            meta: { 
                requiresAuth: true
            }
        },
        {
            path: localPath + '/season',
            name: 'season',
            component: Season,
            meta: { 
                requiresAuth: true
            }
        },
        
        {
            path: localPath + '/calendar/:id',
            name: 'calendar',
            component: Calendar,
            props: true,
            meta: { 
                requiresAuth: true
            }
        },

        {
            path: localPath + '/generate/:id',
            name: 'generate',
            component: Generate,
            props: true,
            meta: { 
                requiresAuth: true
            }
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
            if(store.state.LoggedInUser == ""){
                
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
               var user = store.state.LoggedInUser;
            }
            resolve(user)  // Yay! Everything went well!
        }, 250) ;
       
        getUser.then((user) =>{
            if(user == undefined){
                next({ name: 'home'});
            }
            if(to.meta.requiresRole != undefined && to.meta.requiresRole != ""){
                if(to.meta.requiresRole == "Admin" && to.meta.requiresRole  == user.role){
                    next();
                }else if(to.meta.requiresRole == "User" && (user.role == 'User' || user.role == "Admin" ) ){
                    next();
                }else{
                    store.commit("setErrorMessage", "Je hebt geen toegang tot deze pagina");
                    next({ name: 'home'});
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





