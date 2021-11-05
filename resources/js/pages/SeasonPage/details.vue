<template>
    <div>
        <h1>{{ season.name }}</h1>
        <hr>
         <div class="button-row">
             <button class="btn btn-primary" @click="navigation('home')"><i class="fa fa-home fa-1x" ></i></button>
            <button class="btn btn-primary" @click="navigation('editSeason')" v-if="season.type_member == 'Admin'"><i class="fas fa-pencil-alt fa-1x" ></i></button>
            <button class="btn btn-primary fa" @click="navigation('absence')" v-if="season.is_generated == 0">Afwezigheden</button>
            <button class="btn btn-primary fa" @click="navigation('calendar')" v-if="season.season_draw > 0"><i class="far fa-calendar-alt"></i></button>
            <button class="btn btn-primary fa" @click="navigation('dayCalendar')" v-if="season.is_generated == 1"><i class="fas fa-calendar-day"></i></button>
            <button class="btn btn-primary fa" @click="navigation('generate')"  v-if="season.is_generated == 0 && user.id == season.admin_id">Generate</button>

            <button  v-if="season.season_draw == 0 && season.type_member == 'Admin'" @click.prevent="generateEmptySeason()" class="btn btn-secondary">Create empty season</button>
            <button  v-if="season.season_draw > 0 && season.is_generated == 0" @click.prevent="seasonIsGenerated()" class="btn btn-secondary">Season is generated</button>

            <button  v-if="season.season_draw > 0 && season.type_member == 'Admin'" @click.prevent="deleteCalendar()" class="btn btn-secondary">Delete calendar</button>
            <button v-if="season.is_generated == 0 && season.type_member == 'Admin'" @click.prevent="deleteSeason()" class="btn btn-danger"><i class="fa fa-trash" style="heigth:14px; width:14px"></i></button>
        </div>
        <hr>
        <router-view v-if="season != ''" name="seasonDetails" :season=season></router-view>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';

    export default {
        components: {

        },

        data () {
            return {

            }
        },

        props: {
            'id': '',
         },

         computed: {
            season(){
                return this.$store.state.selectedSeason;
            },

            user(){
                return this.$store.state.loggedInUser;
            }
        },

        methods: {
            getSeason(){
                if(this.id == undefined){
                    return;
                }
                this.$store.dispatch('getSelectedSeason', {id: this.id});
                return;
            },
           
           navigation(name){
                if(this.$route.name == name){
                    return;
                }

                if(name == 'home'){
                    this.$store.dispatch('resetToDefault');
                }
                this.$router.push({name: name, params: { id: this.id },})
           },

           generateEmptySeason(){
                apiCall.getData('season/' +  this.season.id + '/generator/create_empty_season')
                .then(response =>{
                    this.$store.dispatch('getSelectedSeason', {id: this.season.id});
                    this.$store.dispatch('getSelectedCalendar', {id: this.season.id});
                }).catch(() => {
                    console.log('generateEmptySeason: handle server error from here');
                });
            },

            seasonIsGenerated(){
                apiCall.getData('season/' +  this.season.id + '/is_generated' )
                .then(response =>{
                    this.$store.dispatch('getSelectedSeason', {id: this.season.id});
                }).catch(() => {
                    console.log('seasonIsGenerated: handle server error from here');
                });
            },

            deleteCalendar(){
                apiCall.postData('season/' +  this.id + '/generator/' + this.id + '/delete')
                .then(response =>{
                    var message = response.data;
                    if(response.data == false){
                        message = "Calendar cannot be deleted";
                    }else{
                        this.$store.dispatch('getSelectedSeason', {id: this.season.id});
                    }
                    this.$bus.$emit('showMessage', message,  'red', '4000' );
                    
                }).catch(() => {
                    console.log('seasonIsGenerated: handle server error from here');
                });
            },

            deleteSeason(){
                if(confirm('are you sure you want to delete this season ' + this.season.name + '?')){
                    apiCall.postData('season/' +  this.id + '/delete')
                    .then(response =>{
                        this.$bus.$emit('showMessage', response.data,  'red', '4000' );
                        
                        if(response.status == 204){
                            this.$router.push({name: 'season'})
                            return;
                        }
                        
                    }).catch(() => {
                        console.log('deleteSeason: handle server error from here');
                    });
                }
            },
        },

        mounted(){
            this.getSeason();           
        }
    }
</script>

<style scoped>
    button {
        margin: 3px;
    }

    .button-row{
        width: 100%;
        text-align: center;
    }
</style>