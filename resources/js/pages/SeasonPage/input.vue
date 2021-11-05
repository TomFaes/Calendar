<template>
    <div class="container">
        <form @submit.prevent="submit" method="POST" enctype="multipart/form-data">
            <!-- the form items -->
            <global-input type='text' inputName="name" inputId="name" tekstLabel="Naam: " v-model="fields.name" :errors="errors.name" :value='fields.name'></global-input>
            <global-input type='date' inputName="begin" inputId="begin" tekstLabel="Begin: " v-model="fields.begin" :errors="errors.begin" :value='fields.begin' :disabled="disabled"></global-input>
            <global-input type='date' inputName="end" inputId="end" tekstLabel="End: " v-model="fields.end" :errors="errors.end" :value='fields.end' :disabled="disabled"></global-input>
            <global-input type='time' inputName="start_hour" inputId="start_hour" tekstLabel="Start uur: " v-model="fields.start_hour" :errors="errors.start_hour" :value='fields.start_hour' :disabled="disabled"></global-input>
            <global-input type='switchButton' inputName="public" inputId="public" tekstLabel="Public: " v-model="fields.public" :errors="errors.public" :value='fields.public'></global-input>
            <global-input type='switchButton' inputName="allow_replacement" inputId="allow_replacement" tekstLabel="Allow replacements: " v-model="fields.allow_replacement" :errors="errors.allow_replacement" :value='fields.allow_replacement'></global-input>
            
            <!-- Admin multiselect -->
            <div class="row" v-if="multiGroupUsers">
                <div class="col-lg-2 col-md-2 col-sm-0"></div>
                <div class="col-lg-8 col-md-8 col-sm-12">
                    <label>Admin: </label>
                    <multiselect
                        v-model="selectedUser"
                        :multiple="false"
                        :options="multiGroupUsers"
                        :close-on-select="true"
                        :clear-on-select="true"
                        placeholder="Select admin"
                        label="full_name"
                        track-by="full_name"
                    >
                    </multiselect>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-0"></div>
            </div>
            
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
                    :disabled="disabled"
                >
                </multiselect>
            </global-layout>

            <!-- Group multiselect -->
           <global-layout v-if="multiGroups.data">
                <label>Group: </label>
                <multiselect
                    v-model="selectedGroup"
                    :multiple="false"
                    :options="multiGroups.data"
                    :close-on-select="true"
                    :clear-on-select="true"
                    placeholder="Selecteer een groep"
                    label="name"
                    track-by="name"
                    :disabled="disabled"
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
                    'start_hour': '',
                     'type': '',
                     'public': false,
                     'allow_replacement' : false,
                     'disabled': false,
                },
                'formData': new FormData(),
                'disabled': false,
                'errors' : {},
                'multiType': ['None', 'TwoFieldTwoHourThreeTeams', 'SingleFieldOneHourTwoTeams', 'TwoFieldTwoHourFourTeams', 'TestGenerator'],
                'multiGroups': [],
                'selectedGroup':  [],
                'selectedUser': [],
                'selectedType': '',
            }
        },

        props: {
            'season': {},
            'submitOption': ""
         },

         watch:{
            season(){
                this.setData();
            },
         },

         computed: {
             groupUsers(){
                if(this.season === undefined){
                    return;
                }

                if(this.$store.state.selectedGroupUsers.data == undefined){
                    this.$store.dispatch('getSelectedGroupUsers', {groupId: this.season.group_id});
                }
                return this.$store.state.selectedGroupUsers.data;
            },

            multiGroupUsers(){
                if(this.groupUsers === undefined){
                    return;
                }

                if(this.season.group_id != this.groupUsers[0].group_id){
                    this.$store.dispatch('getSelectedGroupUsers', {groupId: this.season.group_id});
                }

                var users = [];
                for(var item in this.groupUsers){
                    if(this.groupUsers[item]['user_id'] > 0){
                         users.push(this.groupUsers[item]);
                    }
                }  
                return users;
            }
        },

        methods: {
            loadGroup(){
                apiCall.getData('user-group')
                .then(response =>{
                    this.multiGroups = response.data;
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },
            
            setFormData(){
                this.formData.set('name', this.fields.name ?? null);
                this.formData.set('begin', this.fields.begin ?? null);
                this.formData.set('end', this.fields.end ?? null);
                this.formData.set('day', this.fields.day ?? null);
                this.formData.set('start_hour', this.fields.start_hour ?? null);
                this.formData.set('admin_id', this.selectedUser.id ?? null);
                this.formData.set('group_id', this.selectedGroup.id ?? null);
                this.formData.set('public', this.fields.public ? 1 : 0);
                this.formData.set('allow_replacement', this.fields.allow_replacement ? 1 : 0);
                this.formData.set('type', this.selectedType ?? 'None');
            },

            submit(){
                if(this.submitOption == 'Create'){
                    return this.create();
                }
                this.update();
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
                    this.resetFormData();
                    this.message = "Your season " + response.data.name + " has been created";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    this.$router.push({name: "seasonDetail", params: { id: response.data.id },});
                }).catch(error => {
                    this.errors = error;
                });
            },

            update(){
                this.setFormData();

                apiCall.updateData('season/' + this.season.id, this.formData)
                .then(response =>{
                    this.$bus.$emit('reloadSeasons');
                    this.message = "Your season " + response.data.name + " has been changed";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    this.resetFormData();
                    this.$router.push({name: "calendar", params: { id: this.season.id },});
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
                this.selectedType = this.season.type;
                this.fields.start_hour = this.season.start_hour;
                 this.fields.public = this.season.public ? true : false;
                this.fields.allow_replacement = this.season.allow_replacement ? true : false;
                this.seasonDraw = this.season.seasonDraw ?? 0;
                if(this.season.season_draw > 0){
                    this.disabled = true;
                }
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
<style scoped>

</style>