<template>
    <div>
        <div v-if=" calendarData['season'] != undefined">
             <h2>Current seasons</h2>
            <div v-for="(calendar) in calendarData['season']" :key="calendar.id">

                <div v-if="calendar['seasonData']['type'] == 'TwoFieldTwoHourThreeTeams'">
                    <two-field-two-hour-three-teams :calendarData="calendar" :userData="calendar['groupUserData']"></two-field-two-hour-three-teams>    
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

    import twoFieldTwoHourThreeTeams from '../NextPlayDayPage/twoFieldTwoHourThreeTeams.vue';

    export default {
        components: {
            Moment,
            twoFieldTwoHourThreeTeams,
        },

        data () {
            return {
                calendarData: {},
            }
        },

        methods: {
            loadSeasonCalendars(){
                apiCall.getData('active_seasons')
                .then(response =>{
                    this.calendarData = response;
                }).catch((error) => {
                    console.log('loadSeasonCalendars:' + error);
                });
            },
        },

        mounted(){
            if(this.$store.state.LoggedInUser != ""){
                this.loadSeasonCalendars();
            }
        }
    }
</script>

<style scoped>

</style>