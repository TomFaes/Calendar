<template>
    <div style="width: 100%">
        
        <div class="container-fluid">
            <nav-bar :user=user ></nav-bar><br>
            <global-layout>
                
                <message-box :message=message></message-box>
                <router-view :key="$route.path" :user=user></router-view>
            </global-layout>
        </div>
    </div>
</template>

<script>
    import navBar from '../IndexPage/navBar.vue';
    import login from '../IndexPage/login.vue';
    import messageBox from '../../components/tools/messageBar.vue';


    export default {
        components: {
            login,
            navBar,
            messageBox, 
        },

        props: {
           // 'auth': {},
         },         

        data () {
            return {

            }
        }, 


        computed: {
            user(){
                return this.$store.state.loggedInUser;
            },
            message(){
                return this.$store.state.message;

            }
        },

        methods: {

        },

        mounted(){
            //this.$store.dispatch('getMessage', {message: "dit is het bericht", color: 'blue', time: 1000});
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
