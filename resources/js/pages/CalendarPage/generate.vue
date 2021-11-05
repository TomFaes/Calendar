<template>
    <div v-if="calendarData['seasonData'] != undefined" >
        <div v-if="calendarData['seasonData']['is_generated'] == 1">
            Dit seizoen is al gemaakt.
        </div>
        <div v-else>
            <h3>Generated season</h3>
            <div v-if="calendarData['seasonData']['type']  == 'TwoFieldTwoHourThreeTeams'">
                <button  class="btn btn-secondary" @click.prevent="generateSeason()">Regenerate season</button>
                <button  class="btn btn-secondary" @click.prevent="saveSeason()">Save season</button>
                <two-field-two-hour-three-teams-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="user"  :generate="true"></two-field-two-hour-three-teams-page>              
            </div>
            <div v-else-if="calendarData['seasonData']['type']  == 'SingleFieldOneHourTwoTeams'">
                <button  class="btn btn-secondary" @click.prevent="generateSeason()">Regenerate season</button>
                <button  class="btn btn-secondary" @click.prevent="saveSeason()">Save season</button>
                <single-field-one-hour-two-teams-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="user"  :generate="true"></single-field-one-hour-two-teams-page>  
            </div>
            <div v-else-if="calendarData['seasonData']['type']  == 'TwoFieldTwoHourFourTeams'">
                <button  class="btn btn-secondary" @click.prevent="generateSeason()">Regenerate season</button>
                <button  class="btn btn-secondary" @click.prevent="saveSeason()">Save season</button>
                <two-field-two-hour-four-teams-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="user"  :generate="true"></two-field-two-hour-four-teams-page>              
            </div>
            <div v-else-if="calendarData['seasonData']['type']  == 'TestGenerator'" class="testGenerator">
                {{  calendarData['seasonData']['seasonDraw'] }}
                <button  class="btn btn-secondary" @click.prevent="generateSeason()">Regenerate season</button>
                <button  class="btn btn-secondary" @click.prevent="saveSeason()">Save season test</button>
                <test-generator-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="user"  :generate="true"></test-generator-page>              
            </div>
            <div v-else>
                onbekende calendar view
            </div>
        </div>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
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
            'season': {},
         },

         watch: {
            season() {
                this.generateSeason();
            }
        },

         computed: {
            user(){
                return this.$store.state.loggedInUser;
            }
        },

        methods: {
            generateSeason(){          
                if(this.season.id == undefined){
                    return;
                }
                apiCall.getData('season/' +  this.season.id + '/generator/new')
                .then(response =>{
                    this.calendarData = response.data;
                    this.message = "Your season is ready";
                    this.$bus.$emit('showMessage', this.message,  'green', '4000' );
                }).catch(() => {
                    console.log('generateSeason: handle server error from here list');
                });
            },

            saveSeason(){
                if(this.calendarData['seasonData']['seasonDraw'] > 0){
                    this.saveGeneratedTeamsSeason();
                    return;
                }

                this.formData.set('jsonSeason', JSON.stringify(this.calendarData));
                apiCall.postData('season/' +  this.season.id + '/generator', this.formData)
                .then(response =>{
                    this.message = "Your season has been created";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    this.$store.dispatch('getSelectedSeason', {id: this.season.id});
                    this.$router.push({name: "calendar", params: { id: this.season.id },});
                }).catch(error => {
                    console.log('saveSeason: handle server error from here list');
                });                
            },

            saveGeneratedTeamsSeason(){
                this.formData.append('teamRange', JSON.stringify(this.calendarData['data']));
                apiCall.postData('season/' +  this.season.id + '/generator/' + this.id, this.formData)
                .then(response =>{
                    this.message = "Your season has been created";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    this.$store.dispatch('getSelectedSeason', {id: this.season.id});
                    this.$router.push({name: "calendar", params: { id: this.season.id },});
                }).catch(error => {
                    console.log('saveGeneratedTeamsSeason: handle server error from here list');
                });
            },
        },

        mounted(){
            this.generateSeason();
        }
    }
</script>

<style scoped>
    div{
        text-align: center;
    }
</style>