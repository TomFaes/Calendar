<template>
    <div>
        <hr>
        <form @submit.prevent="submit" method="POST" enctype="multipart/form-data">
            <global-layout>
                <label>Firstname: </label>
                <input type="text" class="form-control" v-model="fields.firstname" :disabled="disabled == 'disabled'"/>
                <div class="text-danger" v-if="errors">{{ errors.firstname }}</div>
            </global-layout>
            <global-layout>
                <label>Name: </label>
                <input type="text" class="form-control" v-model="fields.name"/>
                <div class="text-danger" v-if="errors">{{ errors.name }}</div>
            </global-layout>
            <global-layout>
                <label>Ignore user during draw: </label><br>
                <Toggle v-model="fields.ignore_user" />
                <div class="text-danger" v-if="errors">{{ errors.ignore_user }}</div>
            </global-layout>
            <global-layout>
                <label>Ignore amount of played games: </label><br>
                <Toggle v-model="fields.ignore_plays" />
                <div class="text-danger" v-if="errors">{{ errors.ignore_plays }}</div>
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
    import Toggle from '@vueform/toggle';

    export default {
        components: {
            Toggle,
        },

         data () {
            return {
                'fields' : {
                    firstname: '',
                    name: '',
                    'ignore_user': '',
                    'ignore_plays': '',
                },
                'errors' : [],
                'formData': new FormData(),
                'disabled': '',
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
                this.formData.set('ignore_user', this.fields.ignore_user ? 1 : 0);
                this.formData.set('ignore_plays', this.fields.ignore_plays ? 1 : 0);
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
                this.fields.ignore_user = this.groupUser.ignore_user ? true : false;
                this.fields.ignore_plays = this.groupUser.ignore_plays ? true : false;
            }
        },

        mounted(){
            if(this.groupUser != undefined){
                this.setData();
                if(this.groupUser.user_id > 0){
                    this.disabled = 'disabled';
                }
            }    
        }
    }
</script>

<style scoped>

</style>
