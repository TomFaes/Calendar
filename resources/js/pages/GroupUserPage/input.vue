<template>
    <div>
        <hr>
        <form @submit.prevent="submit" method="POST" enctype="multipart/form-data">
            <global-layout>
                <label>Firstname: </label>
                <input type="text" class="form-control" v-model="fields.firstname"/>
                <div class="text-danger" v-if="errors">{{ errors.firstname }}</div>
            </global-layout>
            <global-layout>
                <label>Name: </label>
                <input type="text" class="form-control" v-model="fields.name"/>
                <div class="text-danger" v-if="errors">{{ errors.name }}</div>
            </global-layout>
            <br>
            <global-layout center="center">
                 <button class="btn btn-primary">Save user</button>
            </global-layout>
        </form>
        <hr>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';

    export default {
        components: {
            
        },

         data () {
            return {
                'fields' : {
                    firstname: '',
                    name: '',
                },
                'errors' : [],
                'formData': new FormData(),
            }
        },

        props: {
            'groupUser': {},
            'group': {},
            'submitOption': ""
         },

        methods: {
            setFormData(){
                this.formData.set('firstname', this.fields.firstname ?? null);
                this.formData.set('name', this.fields.name ?? null);
                this.formData.set('group_id', this.group.id ?? null);
            },

            submit(){
                if(this.submitOption == 'Create'){
                    this.create();
                }else if(this.submitOption == 'Update'){
                    this.update();
                }else{
                    console.log('geen optie opgegeven');
                }
            },

            create(){
                this.setFormData();
                var action = 'group/' + this.group.id + '/user';

                apiCall.postData(action, this.formData)
                .then(response =>{
                    this.message = "You've added " + this.fields.firstname + " " + this.fields.name + " to " + this.group.name;
                    this.$store.dispatch('getMessage', {message: this.message});
                    this.$store.dispatch('getSelectedGroupUsers', {groupId: this.group.id});
                    this.formData =  new FormData();
                    this.fields = {}; //Clear input fields.
                }).catch(error => {
                    this.errors = error;
                });
            },

            update(){
                this.setFormData();
                var action = 'group/' + this.group.id + '/user/' + this.groupUser.id;

                apiCall.updateData(action, this.formData)
                .then(response =>{
                    this.message = "You've updated the user " + this.fields.firstname + " " + this.fields.name + " for " + this.group.name;
                     this.$store.dispatch('getMessage', {message: this.message});
                    this.$store.dispatch('getSelectedGroupUsers', {groupId: this.group.id});
                    this.formData =  new FormData();
                }).catch(error => {
                        this.errors = error;
                });
            },

            setData(){
                this.fields.firstname = this.groupUser.firstname;
                this.fields.name = this.groupUser.name;
                this.fields.email = this.groupUser.email;
                this.fields.group_id = this.groupUser.group_id;
            }
        },

        mounted(){
            if(this.groupUser != undefined){
                this.setData();
            }
        }
    }
</script>

<style scoped>

</style>
