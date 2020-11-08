<template>
    <div class="container">
        <form @submit.prevent="submit" method="POST" enctype="multipart/form-data">
            <!-- the form items -->
            <global-input type='text' inputName="name" inputId="name" tekstLabel="Naam: " v-model="fields.name" :errors="errors.name" :value='fields.name'></global-input>
            <global-input type='date' inputName="begin" inputId="begin" tekstLabel="Begin: " v-model="fields.begin" :errors="errors.begin" :value='fields.begin'></global-input>
            <global-input type='date' inputName="end" inputId="end" tekstLabel="End: " v-model="fields.end" :errors="errors.end" :value='fields.end'></global-input>
            <global-input type='time' inputName="hour" inputId="hour" tekstLabel="Start uur: " v-model="fields.hour" :errors="errors.hour" :value='fields.hour'></global-input>
             <global-input type='switchButton' inputName="public" inputId="public" tekstLabel="Public: " v-model="fields.public" :errors="errors.public" :value='fields.public'></global-input>
           
            <!-- Admin multiselect -->
            <global-layout v-if="submitOption != 'Create'">
                <label>Admin: </label>
                <multiselect
                    v-model="selectedUser"
                    :multiple="false"
                    :options="multigroupUsers"
                    :close-on-select="true"
                    :clear-on-select="true"
                    placeholder="Select admin"
                    label="fullName"
                    track-by="fullName"
                >
                </multiselect>
            </global-layout>

            <!-- Type multiselect -->
            <global-layout>
                <label>Type: </label>
                <multiselect
                    v-model="selectedType"
                    :multiple="false"
                    :options="multiType"
                    :close-on-select="true"
                    :clear-on-select="true"
                    placeholder="Selecteer een seizoens generator"
                >
                </multiselect>
            </global-layout>

            <!-- Group multiselect -->
           <global-layout v-if="submitOption == 'Create'">
                <label>Group: </label>
                <multiselect
                    v-model="selectedGroup"
                    :multiple="false"
                    :options="multiGroups"
                    :close-on-select="true"
                    :clear-on-select="true"
                    placeholder="Selecteer een groep"
                    label="name"
                    track-by="fullName"
                >
                </multiselect>
           </global-layout>

            <br>
            <global-layout>
                <center>
                    <button class="btn btn-primary">Save</button>
                </center>
                <br>
            </global-layout>
        </form>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import Multiselect from 'vue-multiselect';
    import Moment from 'moment';

    export default {
        components: {
            Multiselect,
            Moment, 
        },

         data () {
            return {
                'fields' : {
                    'id': '',
                    'name': '',
                    'begin': Moment().format('YYYY-MM-DD'),
                    'end': Moment().format('YYYY-MM-DD'),
                    'day': "",
                    'hour': '',
                     'type': '',
                     'public': false,
                },
                'errors' : {},
                'action': '',
                'response': {},
                'selectedUser': [],
                'multigroupUsers': [],
                'selectedGroup': [],
                'multiGroups': [],
                'selectedType': '',
                'multiType': ['None', 'TwoFieldTwoHourThreeTeams'],
                'formData': new FormData(),
                'message': '',
            }
        },

        props: {
            'season': {},
            'submitOption': ""
         },

        methods: {
            setFormData(){
                if(this.fields.name != undefined){
                    this.formData.set('name', this.fields.name);
                }

                if(this.fields.begin != undefined){
                    this.formData.set('begin', this.fields.begin);
                }

                if(this.fields.end != undefined){
                    this.formData.set('end', this.fields.end);
                }

                if(this.fields.day != undefined){
                    this.formData.set('day', this.fields.day);
                }

                if(this.fields.hour != undefined){
                    this.formData.set('hour', this.fields.hour);
                }

                if(this.selectedUser.id > 0){
                    this.formData.set('userId', this.selectedUser.id);
                }

                 if(this.selectedGroup.id > 0){
                    this.formData.set('groupId', this.selectedGroup.id);
                }

                if(this.fields.public != undefined){
                    this.formData.set('public', this.fields.public ? 1 : 0);
                }

                if(this.selectedType != ''){
                    this.formData.set('type', this.selectedType);
                }else{
                    this.formData.set('type', 'None');
                }
            },

            loadGroup(){
                apiCall.getData('user-group')
                .then(response =>{
                    this.multiGroups = response;
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },

            submit(){
                if(this.submitOption == 'Create'){
                    this.create();
                }else if(this.submitOption == 'Update'){
                    this.update();
                }else{
                    this.$bus.$emit('showMessage', "No valid save option given",  'red', '2000' );
                }
            },

            resetFormData(){
                this.fields = {}; //Clear input fields.
                this.errors = {};
                this.formData =  new FormData();
            },

            create(){
                this.setFormData();
                this.action = "season";
                
                apiCall.postData(this.action, this.formData)
                .then(response =>{
                    this.$bus.$emit('reloadSeasons');
                    this.$bus.$emit('resetSeasonDisplay');
                    this.resetFormData();
                    this.message = "Your season " + response.name + " has been created";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                }).catch(error => {
                    this.errors = error;
                });
            },

            update(){
                this.setFormData();
                this.action =  'season/' + this.season.id;


                apiCall.updateData(this.action, this.formData)
                .then(response =>{
                   this.$bus.$emit('reloadSeasons');
                    this.$bus.$emit('resetSeasonDisplay');
                    this.message = "Your season " + response.name + " has been changed";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                }).catch(error => {
                        this.errors = error;
                });
                
            },

            setData(){
                this.fields.name = this.season.name;
                this.fields.begin = this.season.begin;
                this.fields.end = this.season.end;
                this.selectedGroup = this.season.group;
                this.selectedUser = this.season.admin;
                this.multigroupUsers = this.season.group.group_users;
                this.selectedType = this.season.type;
                this.fields.hour = this.season.start_hour;
                this.fields.public = this.season.public ? true : false;
            }
        },

        mounted(){
            this.loadGroup();
            if(this.season != undefined){
                this.setData();
            }
        }
    }
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>

</style>
