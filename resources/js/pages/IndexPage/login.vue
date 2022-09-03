<template>
    <div>
        <span>
            <h3>Welcome back</h3>
        </span><br><br>

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
        <global-layout>
            <a class="social btn btn-block btn-social btn-sm btn-google me-2" :href="baseURL+'/login/google'"><i class="fab fa-google-plus-square"></i>Sign in with Google</a>
            <a class="social btn btn-block btn-social btn-sm btn-microsoft me-2" :href="baseURL+'/login/microsoft'"><i class="fab fa-windows"></i>Sign in with Microsoft</a>
            <a class="social btn btn-block btn-social btn-sm btn-facebook me-2" :href="baseURL+'/login/facebook'"><i class="fab fa-facebook-square"></i>Sign in with Facebook</a>
        </global-layout>
    </div>
</template> 

<script>
    export default {
        components: {

        },

        data (){
            return {
                'fields' : {
                    email: '',
                    password: '',
                },
                'errors' : "",
                'baseURL': '',
                'formData': new FormData(),
            }
        },

        props: {

         },

        methods: {
            setFormData(){
                this.formData.set('email', this.fields.email ?? null);
                this.formData.append('password', this.fields.password ?? null);
            },
            
            handleLogin(){
                this.setFormData();
                axios.get(this.baseURL + '/sanctum/crsf-cookie').then(response =>{
                    axios.post('./api/login', this.formData).then(response => {
                        this.$router.push({name: "home"});
                    }).catch(error => {
                        if (error.response.status === 422) {
                            this.errors =  error.response.data.errors;
                        };
                        if (error.response.status === 404) {
                            var message = error.response.data.message;
                            this.$store.dispatch('getMessage', {message: message, color: 'red', time: 4000});
                        };
                    });
                });
            },
        },

        mounted () {
            this.baseURL = process.env.MIX_APP_URL;
        }
    }

</script>
<style scoped>
    .social{
        margin-left: 5px;
    }
</style>
