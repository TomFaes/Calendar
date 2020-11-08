<template>
    <div  v-if="calendarData['seasonData'] != undefined">
        <div v-if="calendarData['seasonData']['seasonDraw'] > 0">
            Dit seizoen is al gemaakt.
            <router-link :to="{ name: 'calendar', params: { id: id }}" class="btn btn-secondary"><i class="far fa-calendar-alt"></i></router-link>
        </div>
        <div v-else-if="calendarData['seasonData']['type']  == 'TwoFieldTwoHourThreeTeams'">
            <h1>Generate {{  calendarData['seasonData']['name'] }}</h1>
            <button  class="btn btn-secondary" @click.prevent="generateSeason()">Regenerate season</button>
            <button  class="btn btn-secondary" @click.prevent="saveSeason()">Save season</button>
            <two-field-two-hour-three-teams-page :calendarData="calendarData" :userData="calendarData['generateGroupUserData']" :loggedInUser="$attrs.user"  :generate="true"></two-field-two-hour-three-teams-page>              
        </div>
        <div v-else>
            onbekende calendar view
        </div>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import  router from "../../services/router.js";
    import TwoFieldTwoHourThreeTeamsPage from '../CalendarPage/twoFieldTwoHourThreeTeams.vue';
    
    export default {
        components: {
            TwoFieldTwoHourThreeTeamsPage,
        },

        data () {
            return {
                'calendarData': {},
                'formData': new FormData(),
            }
        },
        
         props: {
            'id': '',
         },

        methods: {
            loadSeason(){
                apiCall.getData('season/' +  this.id)
                .then(response =>{
                    this.seasonData = response;
                }).catch(() => {
                    console.log('handle server error from here');
                });
            }, 

            generateSeason(){                
                apiCall.getData('season/' +  this.id + '/generator/new')
                .then(response =>{
                    this.calendarData = response;
                }).catch(() => {
                    console.log('handle server error from here');
                });
                
            },

            saveSeason(){
                 this.formData.set('jsonSeason', JSON.stringify(this.calendarData));
                 apiCall.postData('season/' +  this.id + '/generator', this.formData)
                .then(response =>{
                    this.message = "Your season has been created";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    router.push({ name: "season"});
                }).catch(error => {
                    this.errors = error;
                });                
            },
        },

        mounted(){
            this.generateSeason();
        }
    }
</script>

<style scoped>

</style>
