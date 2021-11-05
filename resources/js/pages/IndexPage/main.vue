<template>
<div>
        <div class="container" v-if="user">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-0">
                    <router-link :to="{ name: 'joinGroup'}" >
                        <div class="tile blue">
                            <div>
                                Join group
                            </div>
                        </div>
                    </router-link>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-0">
                    <router-link :to="{ name: 'group'}" >
                        <div class="tile blue">
                            <div>
                                Groups
                            </div>
                        </div>
                    </router-link>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-0">
                    <router-link :to="{ name: 'season'}" >
                        <div class="tile blue">
                            <div>
                                Seasons
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>

            <hr>
            <div class="container">
                <div class="row">
                    <h2>Active seasons</h2>
                </div>
            </div>
            <hr>
            
            <div class="row" v-for="(season) in activeSeasons['data']"  :key="season.id">
                <h3>{{ season.name }}</h3>
                <div class="col-lg-3 col-md-3 col-sm-0"  ></div>
                
                <div class="col-lg-3 col-md-3 col-sm-6"  >
                        <router-link :to="{ name: 'calendar', params: { id: season.id }}" >
                            <div class="tile orange">
                                <div>
                                    <i class="far fa-calendar-alt fa-2x"></i>
                                </div>
                            </div>
                    </router-link>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6"  >
                    <router-link :to="{ name: 'dayCalendar', params: { id: season.id }}" >
                        <div class="tile orange">
                            <div>
                                <i class="fas fa-calendar-day fa-2x"></i>
                            </div>
                        </div>
                    </router-link>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-0"  ></div>
            </div>

        </div>
    </div>
</template>

<script>
import apiCall from '../../services/ApiCall.js';



    export default {
        components: {
            

        },

        data () {
            return {
                'activeSeasons': {},

            }
        }, 

        computed: {
            user(){
                return this.$store.state.loggedInUser;
            }
        },

        methods: {
            getActiveSeasons(){
                apiCall.getData('active_seasons')
                .then(response =>{
                    this.activeSeasons = response.data;
                }).catch(() => {
                    console.log('getActiveSeasons: handle server error from here');
                });
            }
        },

        mounted(){
            if(this.user){
                this.getActiveSeasons();
            }
        }
    }
</script>

<style scoped>
a {
    text-decoration: none;
 }
 .inactive {
     background-color: grey;
 }
</style>