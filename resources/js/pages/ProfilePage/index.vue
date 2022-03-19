<template>
    <div>
        <div class="row">
                <h2>Profile</h2>
        </div>
        <form @submit.prevent="update" method="POST" enctype="multipart/form-data">
            <global-layout>
                <label>Firstname: </label>
                <input type="text" class="form-control" v-model="user.firstname"/>
                <div class="text-danger" v-if="errors">{{ errors.firstname }}</div>
            </global-layout>
            <global-layout>
                <label>Name: </label>
                <input type="text" class="form-control" v-model="user.name"/>
                <div class="text-danger" v-if="errors">{{ errors.name }}</div>
            </global-layout>
            <global-layout>
                <label>Email: </label>
                <input type="text" class="form-control" v-model="user.email"/>
                <div class="text-danger" v-if="errors">{{ errors.email }}</div>
            </global-layout>
            <br>
            <global-layout center="center">
                <button class="btn btn-primary">Save profile</button>
            </global-layout> 


            <hr>
            <div class="row">
                    <h2>Update Password</h2>
            </div>
            <form @submit.prevent="updatePassword" method="POST" enctype="multipart/form-data">
                <global-layout>
                    <label>Wachtwoord: </label>
                    <input type="password" class="form-control" v-model="formData.password"/>
                </global-layout>
                 <global-layout>
                    <label>Confirm password: </label>
                    <input type="password" class="form-control" v-model="formData.confirm_password"/>
                </global-layout>
                <global-layout>
                    <button class="btn btn-primary">Update password</button>
                </global-layout>
            </form>

            <br>
            <div class="form-group">
                <global-layout center="center">
                    If you want to remove your account click the delete button, all your data will be randomised and you wont be able to use this account again.<br>
                    <button class="btn btn-danger" @click.prevent="removeProfile()"><i class="fas fa-trash fa-1x" ></i></button>
                </global-layout> 
            </div>
        </form>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';

    export default {
        components: {

        },

         data () {
            return {
                'errors' : [],
                'formData': new FormData()
            }
        },

        computed: {
            user(){
                return this.$store.state.loggedInUser;
            }
        },

        methods: {
            setFormData(){
                this.formData.set('firstname', this.user.firstname ?? null);
                this.formData.append('name', this.user.name ?? null);
                this.formData.set('email', this.user.email ?? null);
            },

            update(){
                this.setFormData();
                apiCall.postData('profile', this.formData)
                .then(response =>{
                    this.message = "You've updated your profile ";
                    this.$store.dispatch('getMessage', {message: this.message});
                    this.formData =  new FormData();
                    this.$router.push({name: "home"});
                }).catch(error => {
                    this.errors = error;
                });
            },

            updatePassword(){
                this.formData.set('password', this.formData.password ?? null);
                this.formData.append('confirm_password', this.formData.confirm_password ?? null);



                axios.post('./api/update-password', this.formData).then(response => {
                    var message = response.data.message;
                     this.$store.dispatch('getMessage', {message: message});
                    //this.$bus.$emit('showMessage', message,  'green', '2000' );
                    this.formData =  new FormData();
                    this.$router.push({name: "home"});
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors =  error.response.data.errors;
                    };
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
        },

        mounted(){

        }
    }
</script>

<style scoped>

</style>