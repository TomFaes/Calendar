<template>
    <div>
        <div v-if="countSeasons == usersLoaded && countSeasons == absencesLoaded">
             <h2>Current seasons</h2>
            <div v-for="(calendar , index) in calendarData['season']" :key="calendar.id">
                <div v-if="calendar['seasonType'] == 'TwoFieldTwoHourThreeTeams'">
                    <two-field-two-hour-three-teams :calendarData="calendar" :absenceData="absenceData[index] " :userData="userData[index]"></two-field-two-hour-three-teams>    
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
                userData: {},
                absenceData: {},
                usersLoaded: 0,
                absencesLoaded: 0,
                countSeasons: 100, 
            }
        },

        methods: {
            loadSeasonCalendars(){
                apiCall.getData('active_seasons')
                .then(response =>{
                    this.calendarData = response;
                    this.countSeasons = this.calendarData['season'].length
                    if(response['season'] != undefined){
                        this.countSeasons = this.calendarData['season'].length
                        for(let keySeason in response['season'] ){
                            this.loadUsers(response['season'][keySeason]['seasonId'], keySeason);
                            this.loadAbsences(response['season'][keySeason]['seasonId'], keySeason);
                        }
                    }
                }).catch((error) => {
                    console.log('loadSeasonCalendars:' + error);
                });
            },

            loadUsers(seasonId, keySeason){               
                apiCall.getData('season/' +  seasonId + '/generator/users')
                .then(response =>{
                    this.userData[keySeason] = response;
                    this.usersLoaded++;
                }).catch((error) => {
                    console.log('loadUsers: ' + error);
                });
            },

            loadAbsences(seasonId, keySeason){
                apiCall.getData('season/' +  seasonId + '/generator/absences')
                .then(response =>{
                    if(response.length != 0){
                        this.absenceData[keySeason] = response;
                    }
                    this.absencesLoaded++;
                }).catch((error) => {
                    console.log('loadAbsences: ' + error);
                });
            },
        },

        mounted(){
            


            this.loadSeasonCalendars();

        }
    }
</script>

<style scoped>

</style>