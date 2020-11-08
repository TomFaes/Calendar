<template>
    <div>
        <hr>
       <table class="table table-hover table-sm" v-if="dataList">
            <thead>
                <tr>
                    <th>Name</th>
                    <th  class="d-none d-sm-table-cell">Type</th>
                    <th>Periode</th>
                    <th>Wanneer</th>
                    <th class="d-none d-sm-table-cell">Public</th>
                    <th class="d-none d-sm-table-cell">Admin</th>
                    <th class="d-none d-sm-table-cell">Groep</th>
                     <th>Options</th>                  
                </tr>
            </thead>
            <tbody  v-for="data in dataList"  :key="data.id" >
                    <tr>
                        <td>{{ data.name }}</td>
                         <td class="d-none d-sm-table-cell">{{ data.type }}</td>
                        <td>{{ convertDate(data.begin) }} tot  {{ convertDate(data.end) }}</td>
                        <td>{{ data.day }} {{ convertTime(data.start_hour) }}</td>
                        <td class="d-none d-sm-table-cell">{{ data.public ? 'Yes' : 'No'}}</td>
                        <td class="d-none d-sm-table-cell">{{ data.admin.fullName }}</td>
                        <td class="d-none d-sm-table-cell">{{ data.group.name }}</td>
                        <td>
                            <button v-if="user.id == data.admin_id" class="btn btn-primary" @click.prevent="editSeason(data.id)"><i class="fa fa-edit" style="heigth:14px; width:14px"></i></button>
                            <button v-if="user.id == data.admin_id" class="btn btn-danger" @click.prevent="deleteSeason(data)"><i class="fa fa-trash" style="heigth:14px; width:14px"></i></button>
                            <button v-if="data.seasonDraw == 0"  class="btn btn-secondary" @click.prevent="absences(data.id)">Afwezigheden</button>
                            <router-link v-if="data.seasonDraw == 0 && user.id == data.admin_id" :to="{ name: 'generate', params: { id: data.id }}" class="btn btn-secondary">Generate</router-link>
                            <router-link v-if="data.seasonDraw > 0" :to="{ name: 'calendar', params: { id: data.id }}" class="btn btn-secondary"><i class="far fa-calendar-alt"></i></router-link>
                        </td>
                    </tr>
                    <tr v-if="updateField == data.id">
                        <td colspan="100%">
                            <inputForm  v-if="updateField == data.id" :season=data :submitOption="'Update'"></inputForm>
                        </td>
                    </tr>
                     <tr v-if="showAbsence == data.id">
                        <td colspan="100%">
                            <absenceForm  v-if="showAbsence == data.id" :season=data></absenceForm>
                        </td>
                    </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import inputForm from '../SeasonPage/input';
    import absenceForm from '../SeasonPage/absence.vue';
    import ButtonInput from '../../components/ui/form/ButtonInput.vue';
    import Moment from 'moment';

    export default {
        data () {
            return {
                'dataList' : { },
                'updateField' : 0,
                'selectedGroup': 0,
                'showAbsence': 0,
            }
        },

        components: {
            inputForm,
            ButtonInput,
            Moment,
            absenceForm, 
        },

        methods: {
            loadList(){
                apiCall.getData('season')
                .then(response =>{
                    this.dataList = response;
                    this.updateField = '';
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },

            editSeason(id){
                if( this.updateField == id){
                    this.updateField = 0;
                }else{
                    this.updateField = id;
                }
            },

            deleteSeason(season){
                if(confirm('are you sure you want to delete this season ' + season.name + '?')){
                    apiCall.postData('season/' + season.id + '/delete')
                    .then(response =>{
                        this.message = "Seizoen  " + season.name + " is verwijderd";
                        this.$bus.$emit('showMessage', this.message ,  'red', '2000' );
                        this.loadList();
                    }).catch(() => {
                        console.log('handle server error from here');
                    });
                }
            },

            absences(id){
                if( this.showAbsence == id){
                    this.showAbsence = 0;
                }else{
                    this.showAbsence = id;
                }
            },

            convertDate(value){
                return Moment(value, "YYYY-MM-DD").format('DD/MM/YYYY');
            },

            convertTime(value){
                return Moment(value, "HH:mm:ss").format('HH:mm');
            },
        },

        mounted(){
            this.loadList();
            this.$bus.$on('reloadSeasons', () => {
                this.loadList();
            });
            this.user =  this.$store.state.LoggedInUser;
        }
    }
</script>

<style scoped>

</style>
