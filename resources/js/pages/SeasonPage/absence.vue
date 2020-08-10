<template>
    <div class="container">
        <div class="row">
            <div v-for="(data, index) in playdateList"  :key="index" class="col-lg-2 col-md-2 col-sm-3 col-3 margin-around">
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
                'updateField' : 0,
                'selectedGroup': 0,
                'formData': new FormData(),
                'absenseList': {},
                'user': {},
            }
        },

        components: {
            Moment,
        },

        props: {
            'season': {},
         },

        methods: {
            loadPlayDateList(){
                apiCall.getData( 'season/' + this.season.id + "/generator/play_dates")
                .then(response =>{
                    this.playdateList = response;
                    this.loadPlayerAbsenceList();
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },

            loadPlayerAbsenceList(){
                apiCall.getData( 'season/' + this.season.id + "/absence")
                .then(response =>{
                   this.absenseList = response;
                   this.playdateList .forEach((playData, index) =>{
                       this.$set(this.playdateList[index], 'absenceId', 0); 
                       this.absenseList.forEach(element => {
                           if(playData.date == element.date){
                               this.playdateList[index]['absenceId'] = element.id;
                           }
                        });
                   });
                }).catch(() => {
                    console.log('handle server error from here list');
                });
            },

            addAbsence(data){
                this.formData.set('date', data.date);
                apiCall.postData( 'season/' + this.season.id + "/absence", this.formData)
                .then(response =>{
                    this.formData =  new FormData();
                    this.playdateList .forEach((playData, index) =>{
                        if(playData.date == response.date){
                            this.playdateList[index]['absenceId'] = response.id;
                        }
                    });
                }).catch(error => {
                    this.errors = error;
                });
            },

            deleteAbsence(data, index){
                apiCall.postData('absence/' + data + '/delete')
                .then(response =>{
                    this.response = response;
                    this.playdateList[index]['absenceId'] = 0;
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },

            convertDate(value){
                return Moment(value, "YYYY-MM-DD").format('DD/MM/YYYY');
            },
        },

        mounted(){
            this.loadPlayDateList();
        }
    }
</script>

<style scoped>
.margin-around{
    margin-bottom: 5px !important;
    margin-left: 10px ;
}

</style>