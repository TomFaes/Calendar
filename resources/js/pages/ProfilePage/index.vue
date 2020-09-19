<template>
    <div>
        <div class="row">
                <h2>Profile</h2>
        </div>
        <form @submit.prevent="update" method="POST" enctype="multipart/form-data">
            <!-- the form items -->
            <global-input type='text' inputName="firstname" inputId="firstname" tekstLabel="firstname: " v-model="fields.firstname" :errors="errors.firstname" :value='fields.firstname'></global-input>
            <global-input type='text' inputName="name" inputId="name" tekstLabel="name: " v-model="fields.name" :errors="errors.name" :value='fields.name'></global-input>
            <global-input type='text' inputName="email" inputId="email" tekstLabel="email: " v-model="fields.email" :errors="errors.email" :value='fields.email'></global-input>
            <button-input btnClass="btn btn-primary">Save profile</button-input>

            <div class="form-group">
                <global-layout>
                    If you want to remove your account click the delete button, all your data will be randomised and you wont be able to use this account again.<br>
                    <button class="btn btn-danger" @click.prevent="removeProfile()"><i class="fas fa-trash fa-1x" ></i></button>
                </global-layout> 
            </div>
        </form>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';

    import TextInput from '../../components/ui/form/TextInput.vue';
    import DropdownInput from '../../components/ui/form/DropdownInput.vue';
    import ButtonInput from '../../components/ui/form/ButtonInput.vue';

    export default {
        components: {
            TextInput,
            DropdownInput,
            ButtonInput
        },

         data () {
            return {
                'fields' : {
                    firstname: '',
                    name: '',
                    email: '',
                },
                'user': {},
                'errors' : [],
                'action': '',
                'formData': new FormData()
            }
        },

        methods: {
            loadProfile(){
                apiCall.getData('profile')
                .then(response =>{
                    this.user = response;
                    this.setData();
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },

            setFormData(){
                if(this.fields.firstname != undefined){
                    this.formData.set('firstname', this.fields.firstname);
                }
                if(this.fields.name != undefined){
                    this.formData.append('name', this.fields.name);
                }
                if(this.fields.email != undefined){
                    this.formData.set('email', this.fields.email);
                }
            },

            update(){
                this.setFormData();
                this.action =  'profile',

                apiCall.postData(this.action, this.formData)
                .then(response =>{
                    this.message = "You've updated your profile ";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    this.formData =  new FormData();
                    window.location.href = "./";
                }).catch(error => {
                    this.errors = error;
                });
            },

            removeProfile(){
                if(confirm('are you sure?')){
                    apiCall.postData('profile/delete')
                    .then(response =>{
                        window.location.href = "./logout";
                    }).catch(() => {
                        console.log('handle server error from here');
                    });
                }
            },

            setData(){
                this.fields.firstname = this.user.firstname;
                this.fields.name = this.user.name;
                this.fields.email = this.user.email;
            }
        },

        mounted(){
            this.loadProfile();
        }
    }
</script>

<style scoped>

</style>

