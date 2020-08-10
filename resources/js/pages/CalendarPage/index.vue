<template>
    <div>
        <div v-if="seasonData.type == 'TwoFieldTwoHourThreeTeams'">
            <h1>{{  seasonData.name }}</h1>
                <two-field-two-hour-three-teams-page :seasonData=seasonData :calendarData=calendarData :userData=userData :absenceData=absenceData></two-field-two-hour-three-teams-page>   
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
                'display': "",
                'seasonData': {},
                'calendarData': {},
                'userData': {},
                'absenceData': {},
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

            loadSeasonCalendar(){
                apiCall.getData('season/' +  this.id + '/generator')
                .then(response =>{
                    this.calendarData = response;
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },

            loadUsers(){
                apiCall.getData('season/' +  this.id + '/generator/users')
                .then(response =>{
                    this.userData = response;
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },

            loadAbsences(){
                apiCall.getData('season/' +  this.id + '/generator/absences')
                .then(response =>{
                    this.absenceData = response;
                }).catch(() => {
                    console.log('handle server error from here');
                });
            }
        },

        mounted(){
            this.loadSeasonCalendar();
            this.loadSeason();
            this.loadUsers();
            this.loadAbsences();
        }
    }
</script>

<style scoped>

</style>
