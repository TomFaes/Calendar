<template>
    <div style="width: 100%">
        <div class="container-fluid">
            <global-layout>
                <nav-bar :user=user></nav-bar><br>
                <message-box></message-box>
                <unverified-user v-if="auth == true && displayNav == ''"></unverified-user>
                <router-view :key="$route.path" :user=user></router-view>
                <login   login v-if="displayNav == 'login' && auth == false "></login>
            </global-layout>
        </div>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import navBar from '../IndexPage/navBar.vue';
    import login from '../IndexPage/login.vue';
    import messageBox from '../../components/tools/messageBar.vue';

    import unverifiedUser from '../GroupUserPage/unverifiedUser';

    export default {
        components: {
            login,
            navBar,
            messageBox, 
            unverifiedUser
        },

        props: {
            'auth': {},
         },         

        data () {
            return {
                user: {},
                displayNav: "",
                display: "",
            }
        }, 

        watch:{
            //on a change of route, check for a message from the store
            $route (to, from){
                 if(this.$store.state.errorMessage != ""){
                    this.$bus.$emit('showMessage', this.$store.state.errorMessage,  'red', '2000' );
                    this.$store.commit("setErrorMessage", "");
                }
            }
        },

        methods: {
            //Navigation options
            setDisplay(display = ""){
                this.display = display;
            },

            setDisplayNav(display = ""){
                this.displayNav = display;
                this.setDisplay();
            },
           
            /**
             * Api Calls for
             */
            getLoggedInUser(){
                apiCall.getData('profile')
                .then(response =>{
                    this.user = response;
                }).catch(() => {
                    console.log('getLoggedInUser: handle server error from here');
                });
            },
        },

        mounted(){
            if(this.auth == true){
                this.getLoggedInUser();
            }

            this.$bus.$on('display', value => {
                this.setDisplay(value);
            });

            this.$bus.$on('displayNav', value => {
                this.setDisplayNav(value);
            });   
           //this.$bus.$emit('showMessage', "Test bericht moet verwijderd worden na testen",  'red', '20000' );
        }
    }
</script>

<style scoped>

button {
    margin: 5px;
}

.button-row{
    width: 100%;
    text-align: center;
}

.group {
    background-color: orange;
    margin: 5px;
}

button{
    width: 10%;
}
</style>
