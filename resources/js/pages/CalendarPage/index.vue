<template>
    <div v-if="calendarData['seasonData'] != undefined">
        <div v-if="calendarData['seasonData']['type'] == 'TwoFieldTwoHourThreeTeams'">
            <h1>{{  calendarData['seasonData']['name'] }}</h1>
                <two-field-two-hour-three-teams-page :calendarData="calendarData" :userData="calendarData['groupUserData']"></two-field-two-hour-three-teams-page>   
        </div>
        <div v-else>
            onbekende calendar view
        </div>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import TwoFieldTwoHourThreeTeamsPage from '../CalendarPage/twoFieldTwoHourThreeTeams.vue';

    export default {
        components: {
            TwoFieldTwoHourThreeTeamsPage,
        },

        data () {
            return {
                'calendarData': {},
            }
        },
        
         props: {
            'id': '',
         },

        methods: {
            loadSeasonCalendar(){
                apiCall.getData('season/' +  this.id + '/generator')
                .then(response =>{
                    this.calendarData = response;
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },
        },

        mounted(){
            this.loadSeasonCalendar();
        }
    }
</script>

<style scoped>

</style>
