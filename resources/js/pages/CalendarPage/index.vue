<template>
    <div>
        <div v-if="statusCode == 200">
            <div v-if="calendarData['seasonData'] != undefined">
                <div v-if="calendarData['seasonData']['type'] == 'TwoFieldTwoHourThreeTeams'">   
                    <two-field-two-hour-three-teams-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="user"></two-field-two-hour-three-teams-page>
                </div>
                <div v-else-if="calendarData['seasonData']['type'] == 'SingleFieldOneHourTwoTeams'">    
                    <single-field-one-hour-two-teams-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="user"></single-field-one-hour-two-teams-page>          
                </div>
                <div v-else-if="calendarData['seasonData']['type'] == 'TwoFieldTwoHourFourTeams'">   
                    <two-field-two-hour-four-teams-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="user"></two-field-two-hour-four-teams-page>              
                </div>
                <div v-else-if="calendarData['seasonData']['type'] == 'TestGenerator'">  
                    <test-generator-page :calendarData="calendarData" :userData="calendarData['groupUserData']" :loggedInUser="user"></test-generator-page>
                </div>
                <div v-else>
                    onbekende calendar view
                </div>
            </div>
            <div v-else>
                onbekende calendar view
            </div>
        </div>
        <div v-else-if="statusCode == 0">
             
         </div>
        <div v-else-if="statusCode == 203">
            <h1>THIS IS NOT A PUBLIC SEASON</h1>
        </div>
    </div>
</template>

<script>
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

            }
        },

        watch: {
            season() {
                this.loadSeasonCalendar();
            }
        },

        computed: {
            user(){
                return this.$store.state.loggedInUser;
            },

            calendarData(){
                return this.$store.state.selectedCalendar.data;
            },

            statusCode(){
                return this.$store.state.selectedCalendar.status;
            }
        },
        
        props: {
            'season': '',
        },

        methods: {
            loadSeasonCalendar(){
                if(this.season.id == undefined){
                    return;
                }
                this.$store.dispatch('getSelectedCalendar', {id: this.season.id});
            },
        },
       
        mounted(){

        }
    }
</script>

<style scoped>

</style>