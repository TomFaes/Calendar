<template>
    <div>
        <div v-if="calendarData != undefined">
            <div v-if="calendarData['currentPlayDay'] == undefined">
                <h2 v-if="Date.now() > new Date(season.begin)">Season is over</h2>
                <h2 v-if="Date.now() < new Date(season.begin)">Season will begin on {{convertDate(season.begin)}}</h2>
            </div>
            <div v-else-if="calendarData['seasonData'] != undefined">
                <h2>day view</h2>
                <div v-if="calendarData['seasonData']['type'] == 'TwoFieldTwoHourThreeTeams'">
                    <two-field-two-hour-three-teams :calendarData="calendarData" :userData="calendarData['groupUserData']"></two-field-two-hour-three-teams>    
                </div>
                <div v-else-if="calendarData['seasonData']['type'] == 'TwoFieldTwoHourFourTeams'">
                    <two-field-two-hour-four-teams :calendarData="calendarData" :userData="calendarData['groupUserData']"></two-field-two-hour-four-teams>    
                </div>
                <div v-else-if="calendarData['seasonData']['type'] == 'SingleFieldOneHourTwoTeams'">
                    <single-field-one-hour-two-teams :calendarData="calendarData" :userData="calendarData['groupUserData']"></single-field-one-hour-two-teams>    
                </div>
                <div v-else-if="calendarData['seasonData']['type'] == 'TestGenerator'">
                    <test-generator :calendarData="calendarData" :userData="calendarData['groupUserData']"></test-generator>    
                </div>
                <div v-else>
                    onbekende calendar view
                </div>
            </div>
        </div>
    </div>   
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import Moment from 'moment';

    import TwoFieldTwoHourThreeTeams from '../NextPlayDayPage/twoFieldTwoHourThreeTeams.vue';
    import TwoFieldTwoHourFourTeams from '../NextPlayDayPage/twoFieldTwoHourFourTeams.vue';
    import SingleFieldOneHourTwoTeams from '../NextPlayDayPage/singleFieldOneHourTwoTeams.vue';
    import TestGenerator from '../NextPlayDayPage/testGenerator.vue';

    export default {
        components: {
            Moment,
            TwoFieldTwoHourThreeTeams,
            TwoFieldTwoHourFourTeams,
            SingleFieldOneHourTwoTeams,
            TestGenerator
        },

        data () {
            return {
                //'calendarData': {},
                'statusCode' : 200,
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
        },

        props: {
            season: "",
        },

        methods: {
            loadSeasonCalendar(){
                if(this.season.id == undefined){
                    return;
                }
                if(this.calendarData != undefined){
                    return;
                }
                this.$store.dispatch('getSelectedCalendar', {id: this.season.id});
            },

            convertDate(value){
                return Moment(value, "YYYY-MM-DD").format('DD/MM/YYYY');
            },
        },

        mounted(){
            /*
            if(this.$store.state.LoggedInUser != ""){
                this.loadSeasonCalendars();
            }
            */
        }
    }
</script>

<style scoped>

</style>