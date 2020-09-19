<template>
    <div>
        <span>
            <h3>Welcome back</h3>
        </span><br><br>


        <form @submit.prevent="submit" method="POST" enctype="multipart/form-data">
            <!-- the form items -->
            <global-input sizeForm="small" type='email' inputName="email" inputId="email" tekstLabel="E-mail: " v-model="fields.email" :value='fields.email'></global-input>
            <global-input  sizeForm="small" type='password' inputName="password" inputId="password" v-model="fields.password" tekstLabel="Wachtwoord: " :value='fields.password'></global-input>
            <global-layout sizeForm="small">
                <span> {{ errors }} </span><br>
            </global-layout>
           
            <center>
                    <button class="btn btn-primary">Login</button>
            </center>
        </form><br>

        <global-layout sizeForm="small" v-if="envirement == 'development'">
            <a class="social btn btn-block btn-social btn-sm btn-google" href="http://localhost/tenniscalendar/public_html/login/google"><i class="fab fa-google-plus-square"></i>Sign in with Google</a>
            <a class="social btn btn-block btn-social btn-sm btn-microsoft" href="http://localhost/tenniscalendar/public_html/login/microsoft"><i class="fab fa-windows"></i>Sign in with Microsoft</a>
            <a class="social btn btn-block btn-social btn-sm btn-facebook" href="http://localhost/tenniscalendar/public_html/login/facebook"><i class="fab fa-facebook-square"></i>Sign in with Facebook</a>
        </global-layout>
        <global-layout sizeForm="small" v-else>
            <a class="social btn btn-block btn-social btn-sm btn-google" href="https://tenniskalender.000webhostapp.com/login/google"><i class="fab fa-google-plus-square"></i>Sign in with Google</a>
            <a class="social btn btn-block btn-social btn-sm btn-microsoft" href="https://tenniskalender.000webhostapp.com/login/microsoft"><i class="fab fa-windows"></i>Sign in with Microsoft</a>
            <a class="social btn btn-block btn-social btn-sm btn-facebook" href="https://tenniskalender.000webhostapp.com/login/facebook"><i class="fab fa-facebook-square"></i>Sign in with Facebook</a>
        </global-layout>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';

    import EmailInput from '../../components/ui/form/EmailInput.vue';
    import PasswordInput from '../../components/ui/form/PasswordInput.vue';

    export default {
        components: {
            EmailInput,
            PasswordInput,
        },

        data (){
            return {
                "envirement": "",
                'fields' : {
                    name: '',
                    password: '',
                },
                'errors' : "",
                'formData': new FormData(),
            }
        },

        props: {

         },

        methods: {
            setFormData(){
                if(this.fields.email != undefined){
                    this.formData.set('email', this.fields.email);
                }
                if(this.fields.password != undefined){
                    this.formData.append('password', this.fields.password);
                }
            },

            submit(){
                this.setFormData();
                axios.post('http://localhost/tenniscalendar/public_html/login/standard', this.formData).
                then(function(result){
                    window.location.href = "http://localhost/tenniscalendar/public_html/"
                }).catch(error => {
                    this.errors = "Gebruikers naam of wachtwoord is fout";
                });
            },
        },

        mounted () {
            this.envirement = process.env.NODE_ENV;
        }
    }

</script>
<style scoped>

</style>
