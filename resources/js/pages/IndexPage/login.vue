<template>
    <div>
        <span>
            <h3>Welcome back</h3>
        </span><br><br>

        <!--
        <form @submit.prevent="submit" method="POST" enctype="multipart/form-data">
        -->
        <form @submit.prevent="handleLogin">
            <!-- the form items -->
            <global-layout>
                <label>Email: </label>
                <input type="email" class="form-control" v-model="fields.email"/>
            </global-layout>
            <global-layout>
                <label>Wachtwoord: </label>
                <input type="password" class="form-control" v-model="fields.password"/>
            </global-layout>
             <global-layout>
                 <div class="text-danger">
                    {{ errors }}
                 </div>
            </global-layout>

           
           <global-layout center="center">
                    <button class="btn btn-primary">Login</button>
           </global-layout>
        </form><br>

        <global-layout v-if="envirement == 'development'">
            <a class="social btn btn-block btn-social btn-sm btn-google" href="http://localhost/tenniscalendar/public_html/login/google"><i class="fab fa-google-plus-square"></i>Sign in with Google</a>
            <a class="social btn btn-block btn-social btn-sm btn-microsoft" href="http://localhost/tenniscalendar/public_html/login/microsoft"><i class="fab fa-windows"></i>Sign in with Microsoft</a>
            <a class="social btn btn-block btn-social btn-sm btn-facebook" href="http://localhost/tenniscalendar/public_html/login/facebook"><i class="fab fa-facebook-square"></i>Sign in with Facebook</a>
        </global-layout>
        <global-layout v-else>
            <a class="social btn btn-block btn-social btn-sm btn-google" href="https://tenniskalender.000webhostapp.com/login/google"><i class="fab fa-google-plus-square"></i>Sign in with Google</a>
            <a class="social btn btn-block btn-social btn-sm btn-microsoft" href="https://tenniskalender.000webhostapp.com/login/microsoft"><i class="fab fa-windows"></i>Sign in with Microsoft</a>
            <a class="social btn btn-block btn-social btn-sm btn-facebook" href="https://tenniskalender.000webhostapp.com/login/facebook"><i class="fab fa-facebook-square"></i>Sign in with Facebook</a>
        </global-layout>
    </div>
</template> 

<script>
    import apiCall from '../../services/ApiCall.js';


    export default {
        components: {

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
                var baseUrl = "https://tenniskalender.000webhostapp.com";
                if(this.envirement == "development"){
                    baseUrl = "http://localhost/tenniscalendar/public_html"
                }
                
                this.setFormData();
                axios.post( baseUrl + '/login/standard', this.formData).
                then(function(result){
                    window.location.href = baseUrl;
                }).catch(error => {
                    this.errors = "Gebruikers naam of wachtwoord is fout";
                });
            },

            handleLogin(){
                this.setFormData();

                var localPath = process.env.MIX_APP_URL
                axios.get(localPath + 'sanctum/crsf-cookie').then(response =>{
                    axios.post('./api/login', this.formData).then(response => {
                        this.$router.push({name: "home"});
                    }).catch(error => {
                        if (error.response.status === 422) {
                            this.errors =  error.response.data.errors;
                        };
                        if (error.response.status === 404) {
                            var message = error.response.data.message;
                            this.$store.dispatch('getMessage', {message: message, color: 'red', time: 4000});
                            this.loginFailed = error.response.data.message;
                        };
                    });
                });
            },

        },

        mounted () {
            this.envirement = process.env.NODE_ENV;
        }
    }

</script>
<style scoped>
.social{
    margin-left: 5px;
}

</style>
