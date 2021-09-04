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
                            <button v-if="data.is_generated == 0"  class="btn btn-secondary" @click.prevent="absences(data)">Afwezigheden</button>
                            <router-link v-if="data.is_generated == 0 && user.id == data.admin_id" :to="{ name: 'generate', params: { id: data.id }}" class="btn btn-secondary">Generate</router-link>
                            <!-- For now only the new types will have a create empty season -->
                            <button  v-if="data.type == 'TestGenerator' && data.seasonDraw == 0" @click.prevent="generateEmptySeason(data, 2)" class="btn btn-secondary">Create empty season</button>
                            <button  v-if="data.type == 'SingleFieldOneHourTwoTeams' && data.seasonDraw == 0" @click.prevent="generateEmptySeason(data, 2)" class="btn btn-secondary">Create empty season</button>
                            <button  v-if="data.type == 'TwoFieldTwoHourThreeTeams' && data.seasonDraw == 0" @click.prevent="generateEmptySeason(data, 3)" class="btn btn-secondary">Create empty season</button>
                            <button  v-if="data.type == 'TwoFieldTwoHourFourTeams' && data.seasonDraw == 0" @click.prevent="generateEmptySeason(data, 4)" class="btn btn-secondary">Create empty season</button>
                            

                            <button  v-if="data.seasonDraw > 0 && data.is_generated == 0" @click.prevent="seasonIsGenerated(data.id)" class="btn btn-secondary">Season is generated</button>

                            <!-- 'multiType': ['None', 'TwoFieldTwoHourThreeTeams', 'SingleFieldOneHourTwoTeams', 'TwoFieldTwoHourFourTeams', 'TestGenerator'], -->

                            <!--
                            <router-link v-if="data.is_generated == 1" :to="{ name: 'calendar', params: { id: data.id }}" class="btn btn-secondary"><i class="far fa-calendar-alt"></i></router-link>
                            -->
                            <router-link  :to="{ name: 'calendar', params: { id: data.id }}" class="btn btn-secondary"><i class="far fa-calendar-alt"></i></router-link>
                        </td>
                    </tr>
                    <tr v-if="updateField == data.id">
                        <td colspan="100%">
                            <inputForm  v-if="updateField == data.id" :season=data :submitOption="'Update'"></inputForm>
                        </td>
                    </tr>
                     <tr v-if="showAbsence == data.id">
                        <td colspan="100%">
                            <span v-if="user.id == data.admin_id">
                                <button v-for="groupUser in data.group.group_users"  :key="groupUser.id"  class="btn btn-warning absenceButton" @click.prevent="setGroupUser(groupUser)">{{  groupUser.user_id != null ? groupUser.user.firstname : groupUser.firstname }}</button>
                                <hr>
                                <center>
                                    {{  selectedGroupUser.user_id != null ? selectedGroupUser.user.firstname : selectedGroupUser.firstname }}
                                </center>
                            </span>
                            <br>
                            <absenceForm  v-if="showAbsence == data.id" :season=data :selectedGroupUser=selectedGroupUser></absenceForm>
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
                'selectedGroupUser': {},
                'user': [],
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

            generateEmptySeason(data, teams = 0){
                apiCall.getData('season/' +  data.id + '/generator/create_empty_season?teams=' + teams )
                .then(response =>{
                    this.loadList();
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },

            seasonIsGenerated(id){
                apiCall.getData('season/' +  id + '/is_generated' )
                .then(response =>{
                    this.loadList();
                }).catch(() => {
                    console.log('handle server error from here');
                });

            },

            absences(data){
                //console.log(data.group)
                if( this.showAbsence == data.id){
                    this.showAbsence = 0;
                }else{
                    this.showAbsence = data.id;
                    for(var i = 0; i < data.group.group_users.length; i++){
                        if(this.user.id == data.group.group_users[i].user_id){
                            this.selectedGroupUser = data.group.group_users[i];
                            break;
                        }
                    } 
                }
            },

            setGroupUser(groupUser){
                this.selectedGroupUser = groupUser;
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
.absenceButton {
    margin: 2px;
}
</style>
