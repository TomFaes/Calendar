<template>
    <div class="container">
        <form @submit.prevent="submit" method="POST" enctype="multipart/form-data">
            <!-- the form items -->
            <text-input inputName="name" inputId="name" tekstLabel="Name: " v-model="fields.name" :errors="errors.name" :value='fields.name'></text-input>
            <!-- Admin multiselect -->
            <!--
            <div class="row" v-if="submitOption != 'Create'">
                <div class="col-lg-2 col-md-2 col-sm-0"></div>
                <div class="col-lg-8 col-md-8 col-sm-12">
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
                </div>
                <div class="col-lg-2 col-md-2 col-sm-0"></div>
            </div>
            -->
            <br>
            <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-0"></div>
                <div class="col-lg-8 col-md-8 col-sm-12">
                    <center>
                        <button class="btn btn-primary">{{buttonText}}</button>
                    </center>
                    <br>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-0"></div>
            </div>

            <div class="row" v-if="submitOption != 'Create'">
                <div class="col-lg-2 col-md-2 col-sm-0"></div>
                <div class="col-lg-8 col-md-8 col-sm-12">
                    <center>
                        <!--
                        <delete-group :group="group" v-if="submitOption != 'Create'"></delete-group>
                    -->
                    </center>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-0"></div>
            </div>
        </form>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';

    import TextInput from '../../components/ui/form/TextInput.vue';
    import TextArea from '../../components/ui/form/TextAreaInput.vue';
    import Multiselect from 'vue-multiselect';

    export default {
        components: {
            TextInput,
            TextArea,
            Multiselect
        },

         data () {
            return {
                'fields' : {
                    name: '',
                    admin_id: '',
                },
                'errors' : {},
                'action': '',
                'response': {},
                'selectedUser': [],
                'multigroupUsers': [],
                'formData': new FormData(),
                'message': '',
                'buttonText': "Save group"
            }
        },

        props: {
            'group': {},
            'submitOption': ""
         },

        methods: {
            setFormData(){
                if(this.fields.name != undefined){
                    this.formData.set('name', this.fields.name);
                }
                if(this.fields.description != undefined){
                    this.formData.append('description', this.fields.description);
                }
                if(this.selectedUser != undefined){
                    this.formData.set('admin_id', this.selectedUser.id);
                }
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
                this.action = "group";

                apiCall.postData(this.action, this.formData)
                .then(response =>{
                    this.$bus.$emit('reloadGroupList');
                    this.resetFormData();
                    this.message = "Your group " + response.name + " has been created";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                }).catch(error => {
                    this.errors = error;
                });
            },

            update(){
                this.setFormData();
                this.action =  'group/' + this.group.id;

                apiCall.updateData(this.action, this.formData)
                .then(response =>{
                    this.$bus.$emit('reloadGroupList');
                    if(response.name == undefined){
                        this.message = response;
                        this.$bus.$emit('showMessage', this.message,  'red', '2000' );
                    }else{
                        this.message = "Your group " + response.name + " has been changed";
                        this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    }
                    this.$bus.$emit('display', 'groupStats');
                }).catch(error => {
                        this.errors = error;
                });
            },

            setData(){
                this.fields.name = this.group.name;
                this.fields.description = this.group.description;

                var adminUser = "";
                var adminId = this.group.admin.id;
                this.group.group_users.forEach(function(item, index){
                    if(adminId == item.id){
                        adminUser = index;
                    }
                });
                this.selectedUser = this.group.group_users[adminUser];
            }
        },

        mounted(){
            if(this.submitOption == "Create"){
                this.buttonText = "Create group";
            }
            if(this.group != undefined){
                for(var item in this.group.group_users){
                    if(this.group.group_users[item]['user_id'] > 0){
                         this.multigroupUsers.push(this.group.group_users[item]);
                    }
                }
                this.setData();
            }
        }
    }
</script>

<style scoped>

</style>
