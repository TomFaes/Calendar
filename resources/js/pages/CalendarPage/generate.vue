<template>
    <div>
        <div v-if="seasonData.seasonDraw > 0">
            Dit seizoen is al gemaakt.
            <router-link :to="{ name: 'calendar', params: { id: id }}" class="btn btn-secondary"><i class="far fa-calendar-alt"></i></router-link>
        </div>
        <div v-else-if="seasonData.type == 'TwoFieldTwoHourThreeTeams'">
            <h1>Generate {{  seasonData.name }}</h1>
            <button  class="btn btn-secondary" @click.prevent="generateSeason()">Regenerate season</button>
            <button  class="btn btn-secondary" @click.prevent="saveSeason()">Save season</button>
                <two-field-two-hour-three-teams-page :seasonData=seasonData :calendarData=calendarData :userData=userData :absenceData=absenceData></two-field-two-hour-three-teams-page>   
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
                'display': "",
                'seasonData': {},
                'calendarData': {},
                'userData': {},
                'absenceData': {},
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
                    this.loadGroupUsers(this.seasonData.group_id);
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

            loadGroupUsers(groupId){
                apiCall.getData( 'group/' + groupId + '/user')
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
            this.generateSeason();
            this.loadSeason();
            this.loadAbsences();
        }
    }
</script>

<style scoped>

</style>
