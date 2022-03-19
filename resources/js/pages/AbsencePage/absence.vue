<template>
    <div v-if="season.is_generated == 0">
         <div v-if="season.type_member == 'Admin' && loggedInGroupUser">
             <h3>Selecteer een gebruiker</h3>
             <hr>
             <span v-for="groupUser in groupUsers"  :key="groupUser.id">
                <button v-if="selectedGroupUserId == groupUser.id" class="btn btn-warning absenceSelectedButton " @click.prevent="loadPlayerAbsenceList(groupUser.id)">
                    {{  groupUser.user_id != null ? groupUser.user.full_name : groupUser.full_name }}
                </button>
                <button v-else class="btn btn-primary absenceButton" @click.prevent="loadPlayerAbsenceList(groupUser.id)">
                    {{  groupUser.user_id != null ? groupUser.user.full_name : groupUser.full_name }}
                </button>
            </span>
            <hr>
         </div>

        <div v-if=" loggedInGroupUser"></div>
         <div class="row" v-if="playdateList.data">
            <div v-for="(data, index) in playdateList.data"  :key="index" class="col-lg-2 col-md-2 col-sm-3 col-3 margin-around">
                <button v-if="data.absenceId > 0" class="btn btn-danger" @click.prevent="deleteAbsence(data.absenceId, index)">{{ convertDate(data.date) }}</button>
                <button v-else class="btn btn-success" @click.prevent="addAbsence(data)">{{ convertDate(data.date) }}</button>
            </div>
        </div>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import Moment from 'moment';

    export default {
        data () {
            return {
                'playdateList' : { },
                'formData': new FormData(),
                'absenseList': {},
                'selectedGroupUserId': 0,
            }
        },

        components: {
            Moment,
        },

        props: {
            'season': {},
         },

        computed: {
             user(){
                if(this.$store.state.loggedInUser.id == undefined){
                    this.$store.dispatch('getLoggedInUser');
                }
                return this.$store.state.loggedInUser;
            },

            groupUsers(){
                if(this.season.id === undefined){
                    return;
                }

                if(this.$store.state.selectedGroupUsers.data == undefined){
                    this.$store.dispatch('getSelectedGroupUsers', {groupId: this.season.group_id});
                }
                return this.$store.state.selectedGroupUsers.data;
            },

            loggedInGroupUser(){
                if(this.groupUsers  == undefined || this.user == undefined){
                    return;
                }

                var user = undefined;
                this.groupUsers.forEach((groupUser, index) =>{
                    if(groupUser.user_id == this.user.id){
                        user =  this.groupUsers[index];
                        return;
                    }
                });
                this.loadPlayerAbsenceList(user.id);
                this.loadPlayDateList();
                return user;
            }
        },

        methods: {
            loadPlayDateList(){
                if(this.season.id == undefined){
                    return;
                }
                
                apiCall.getData( 'season/' + this.season.id + "/generator/play_dates")
                .then(response =>{
                    this.playdateList = response.data;
                }).catch(() => {
                    console.log('LoadPlayDateList: handle server error from here');
                });
            },

            loadPlayerAbsenceList(groupUserId){        
                apiCall.getData( 'season/' + this.season.id + "/absence")
                .then(response =>{
                    this.absenseList = response.data; 
                    if(response.data[groupUserId] != undefined){
                        this.absenseList = response.data[groupUserId];
                        this.playdateList.data.forEach((playData, index) =>{
                            this.playdateList.data[index]['absenceId'] = 0;
                            this.absenseList.forEach(element => {
                                if(playData.date == element.date){
                                    this.playdateList.data[index]['absenceId'] = element.id;
                                }
                            });
                        });
                    }
                }).catch(() => {
                    console.log('LoadPlayerAbsenceList: handle server error from here list');
                });
                this.selectedGroupUserId = groupUserId;
            },
            
            addAbsence(data){
                this.formData.set('date', data.date);
                if(this.selectedGroupUserId > 0){
                    this.formData.set('group_user_id', this.selectedGroupUserId);
                }else{
                    this.formData.set('group_user_id', this.loggedInGroupUser.id);
                }
                
                apiCall.postData( 'season/' + this.season.id + "/absence", this.formData)
                .then(response =>{
                    this.formData =  new FormData();
                    this.playdateList.data.forEach((playData, index) =>{
                        if(playData.date == response.data.date){
                            this.playdateList.data[index]['absenceId'] = response.data.id;
                        }
                    });
                }).catch(error => {
                    console.log('addAbsence: handle server error from here list');
                });
            },

            deleteAbsence(absenceId, index){
                apiCall.postData('absence/' + absenceId + '/delete')
                .then(response =>{
                    this.playdateList.data[index]['absenceId'] = 0;
                }).catch(() => {
                    console.log('deleteAbsence: handle server error from here');
                });
            },

           convertDate(value){
                return Moment(value, "YYYY-MM-DD").format('DD/MM/YYYY');
            },
        },

        mounted(){
            //this.loadPlayerAbsenceList();
        }
    }
</script>

<style scoped>
    .margin-around{
        margin-bottom: 5px !important;
        margin-left: 10px ;
    }

    .absenceButton {
        /*background-color: yellow;*/
        margin: 2px;
    }

    .absenceSelectedButton {
        background-color: lightgray;
        margin: 2px;
    }
</style>