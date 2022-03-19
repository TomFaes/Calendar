<template>
    <div class="container">
        <form @submit.prevent="submit" method="POST" enctype="multipart/form-data">
            <!-- the form items -->
             <global-layout>
                <label>Naam: </label>
                <input type="text" class="form-control" v-model="fields.name"/>
                <div class="text-danger" v-if="errors">{{ errors.name }}</div>
            </global-layout>
            <!-- Admin multiselect -->    
            <div class="row" v-if="multiGroupUsers">
                <div class="col-lg-2 col-md-2 col-sm-0"></div>
                <div class="col-lg-8 col-md-8 col-sm-12">
                    <label>Admin: </label>
                    <Multiselect
                        v-model="selectedUser"
                        :multiple="false"
                        :options="multiGroupUsers"
                        :close-on-select="true"
                        :clear-on-select="true"
                        placeholder="Select admin"
                        label="full_name"
                        track-by="full_name"
                    />
                </div>
                <div class="col-lg-2 col-md-2 col-sm-0"></div>
            </div>

            <br>
            <global-layout center="center">
                <button class="btn btn-primary">{{buttonText}}</button>
                <br>
            </global-layout>
        </form>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import Multiselect from '@suadelabs/vue3-multiselect';
    
    export default {
        components: {
            Multiselect
        },

         data () {
            return {
                'fields' : {
                    name: '',
                },
                'errors' : {},
                'selectedUser': {},
                //'multiGroupUsers': [],
                'formData': new FormData(),
                'buttonText': "Save group"
            }
        },

        props: {
            'group': {},
            'submitOption': ""
         },

         watch:{
            group(){
                this.setData();
            },
         },

        computed: {
            groupUsers(){
                if(this.group === undefined){
                    return;
                }

                if(this.$store.state.selectedGroupUsers.data == undefined){
                    this.$store.dispatch('getSelectedGroupUsers', {groupId: this.group.id});
                }
                return this.$store.state.selectedGroupUsers.data;
            },

            multiGroupUsers(){
                if(this.groupUsers === undefined){
                    return;
                }

                if(this.group.id != this.groupUsers[0].group_id){
                    this.$store.dispatch('getSelectedGroupUsers', {groupId: this.group.id});
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


            setFormData(){
                this.formData.set('name', this.fields.name ?? null);
                this.formData.set('admin_id', this.selectedUser.id ?? null);
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

                apiCall.postData('group', this.formData)
                .then(response =>{
                    this.resetFormData();
                    var message = "Your group " + response.data.name + " has been created";
                    this.$store.dispatch('getMessage', {message: message});
                    this.$store.dispatch('getUserGroups');
                }).catch(error => {
                    this.errors = error;
                });
            },

            update(){
                this.setFormData();

                apiCall.updateData('group/' + this.group.id, this.formData)
                .then(response =>{
                    if(response.data.name == undefined){
                        this.$store.dispatch('getMessage', {message: response.data, color: 'red', time: 2000});
                    }else{
                        var message = "Your group " + response.data.name + " has been changed";
                        this.$store.dispatch('getMessage', {message: message});
                    }
                    this.$store.dispatch('getSelectedGroup', {id: this.group.id});
                    this.$router.push({name: "groupDetail", params: { id: this.group.id },})
                }).catch(error => {
                        this.errors = error;
                });
            },

            setData(){
                this.fields.name = this.group.name;
                this.selectedUser = this.group.admin;
            }
        },

        mounted(){
            if(this.submitOption == "Create"){
                this.buttonText = "Create group";
            }
            if(this.group != undefined){
                this.setData();
            }
        },
    }
</script>

<style scoped>

</style>
