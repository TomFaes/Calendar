<template>
    <div>
        <div v-if="statusCode == 200">
            <div v-if="calendarData['seasonData'] != undefined">
                <div v-if="calendarData['seasonData']['type'] == 'TwoFieldTwoHourThreeTeams'">   
                    <two-field-two-hour-three-teams-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="$attrs.user"></two-field-two-hour-three-teams-page>              
                </div>
                <div v-else-if="calendarData['seasonData']['type'] == 'SingleFieldOneHourTwoTeams'">    
                    <single-field-one-hour-two-teams-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="$attrs.user"></single-field-one-hour-two-teams-page>          
                </div>

                <div v-else-if="calendarData['seasonData']['type'] == 'TwoFieldTwoHourFourTeams'">   
                    <two-field-two-hour-four-teams-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="$attrs.user"></two-field-two-hour-four-teams-page>              
                </div>

                <div v-else-if="calendarData['seasonData']['type'] == 'TestGenerator'">  
                    <test-generator-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="$attrs.user"></test-generator-page>
                </div>
                
                <div v-else>
                    onbekende calendar view
                </div>
            </div>
        </div>
         <div v-else-if="statusCode == 0">
             
         </div>
        <div v-else> 
            <h1>THIS IS NOT A PUBLIC SEASON</h1>
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
                'statusCode' : 0,
            }
        },
        
         props: {
            'id': '',
         },

        methods: {
            loadSeasonCalendar(){
                apiCall.getDataAdv('season/' +  this.id + '/generator')
                .then(response =>{
                    this.statusCode = response.status;
                    this.calendarData = response.data;
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },
        },

        mounted(){
            this.loadSeasonCalendar();
            this.$bus.$on('reloadCalendar', (groupId) => {
                   this.loadSeasonCalendar(groupId);
            });
        }
    }
</script>

<style scoped>

</style>
