<template>
    <div >
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark fixed-top" id="mainNav">
            <router-link :to="{ name: 'home'}" class=".nav-link js-scroll-trigger">Kalender</router-link>    

            <div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            </div>

            <div class="collapse navbar-collapse" id="navbarResponsive" style="width: 100%">
                <ul class="navbar-nav ms-auto">
                    
                    <!--
                    <li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show" v-if="user.role == 'Admin'" >
                    </li>
                    <li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show" v-if="user.id > 0" >
                        <span class="nav-link js-scroll-trigger item-link"  >
                            <router-link :to="{ name: 'group' }" class=".nav-link js-scroll-trigger">Group</router-link>
                        </span>
                    </li>

                    <li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show" v-if="user.id > 0" >
                        <span class="nav-link js-scroll-trigger item-link"  >
                            <router-link :to="{ name: 'season' }" class=".nav-link js-scroll-trigger">Seasons</router-link>
                        </span>
                    </li>
-->
                    <li class="nav-item"  data-toggle="collapse" data-target=".navbar-collapse.show" v-if="user.id > 0">                        
                          <router-link :to="{ name: 'profile' }" class=".nav-link js-scroll-trigger"><i class="fa fa-user fa-2x "></i></router-link>
                    </li>

                    <li class="nav-item"  data-toggle="collapse" data-target=".navbar-collapse.show" v-if="user.id > 0">
                          <a class="nav-link js-scroll-trigger nav-link js-scroll-trigger item-link" @click.prevent="logout">Logout</a>
                    </li>
                    <li class="nav-item" data-toggle="collapse" data-target=".navbar-collapse.show" v-else>
                         <router-link :to="{ name: 'login'}" class=".nav-link js-scroll-trigger">Login</router-link>
                    </li>
                </ul>
            </div>
        </nav>
        <br><br>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    export default {
        data (){
            return {
                envirement: "",
                
            }
        },

        props: {
            'user': {},
         },

        methods: {
            logout(){                
                if(this.envirement == "development"){
                    return window.location.href = "http://localhost/tenniscalendar/public_html/logout";
                }
                return window.location.href = "https://tenniskalender.000webhostapp.com/logout";
            }
        },

        mounted () {
            this.envirement = process.env.NODE_ENV;
        }
    }

</script>
<style scoped>
a {
    color: lightgray
}
a :hover{
    filter: brightness(80%);
}
</style>
