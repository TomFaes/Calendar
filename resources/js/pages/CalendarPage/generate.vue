<template>
    <div  v-if="calendarData['seasonData'] != undefined">
        <div v-if="calendarData['seasonData']['is_generated'] == 1">
            Dit seizoen is al gemaakt.
            <router-link :to="{ name: 'calendar', params: { id: id }}" class="btn btn-secondary"><i class="far fa-calendar-alt"></i></router-link>
        </div>
        <div v-else-if="calendarData['seasonData']['type']  == 'TwoFieldTwoHourThreeTeams'">
            <h1>Generate {{  calendarData['seasonData']['name'] }}</h1>
            <button  class="btn btn-secondary" @click.prevent="generateSeason()">Regenerate season</button>
            <button  class="btn btn-secondary" @click.prevent="saveSeason()">Save season</button>
            <two-field-two-hour-three-teams-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="$attrs.user"  :generate="true"></two-field-two-hour-three-teams-page>              
        </div>
        <div v-else-if="calendarData['seasonData']['type']  == 'SingleFieldOneHourTwoTeams'">
            <h1>Generate {{  calendarData['seasonData']['name'] }}</h1>
            <button  class="btn btn-secondary" @click.prevent="generateSeason()">Regenerate season</button>
            <button  class="btn btn-secondary" @click.prevent="saveSeason()">Save season</button>
            <single-field-one-hour-two-teams-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="$attrs.user"  :generate="true"></single-field-one-hour-two-teams-page>  
        </div>
        <div v-else-if="calendarData['seasonData']['type']  == 'TwoFieldTwoHourFourTeams'">
            <h1>Generate {{  calendarData['seasonData']['name'] }}</h1>
            <button  class="btn btn-secondary" @click.prevent="generateSeason()">Regenerate season</button>
            <button  class="btn btn-secondary" @click.prevent="saveSeason()">Save season</button>
            <two-field-two-hour-four-teams-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="$attrs.user"  :generate="true"></two-field-two-hour-four-teams-page>              
        </div>
        <div v-else-if="calendarData['seasonData']['type']  == 'TestGenerator'" class="testGenerator">
            <h1>Generate aaaa {{  calendarData['seasonData']['name'] }}</h1>
            {{  calendarData['seasonData']['seasonDraw'] }}
            <button  class="btn btn-secondary" @click.prevent="generateSeason()">Regenerate season</button>
            <button  class="btn btn-secondary" @click.prevent="saveSeason()">Save season test</button>
            <test-generator-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="$attrs.user"  :generate="true"></test-generator-page>              
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
    import SingleFieldOneHourTwoTeamsPage from '../CalendarPage/singleFieldOneHourTwoTeams.vue';
    import TwoFieldTwoHourFourTeamsPage from '../CalendarPage/twoFieldTwoHourFourTeams.vue';
    import TestGeneratorPage from '../CalendarPage/testGenerator.vue';
    
    
    export default {
        components: {
            TwoFieldTwoHourThreeTeamsPage,
            SingleFieldOneHourTwoTeamsPage,
            TwoFieldTwoHourFourTeamsPage,
            TestGeneratorPage
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
                if(this.calendarData['seasonData']['seasonDraw'] > 0){
                    this.saveGeneratedTeamsSeason();
                    return;
                }

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

            saveGeneratedTeamsSeason(){
                this.formData.append('teamRange', JSON.stringify(this.calendarData['data']));
                this.formData.append('updateRange', 'pregeneratedseason');
                apiCall.postData('season/' +  this.id + '/generator/' + this.id, this.formData)
                .then(response =>{
                     this.message = "Your season has been created";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    router.push({ name: 'calendar', params: { id:  this.id }});                   
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
