<template>
    <div>
        <hr>
        <form @submit.prevent="joinGroup" method="POST" enctype="multipart/form-data">
        <global-layout>
                <label>Code to join group: </label>
                <input type="text" class="form-control" v-model="fields.code"/>
                <div class="text-danger" v-if="errors">{{ errors.code }}</div>
            </global-layout>
                <br>
                <global-layout center="center">
                    <button class="btn btn-primary">Add code</button>
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
                    code: '',
                },
                'errors' : [],
                'formData': new FormData(),
            }
        },

        props: {

         },

        methods: {
            joinGroup(){
                if(this.fields.code == undefined){
                    return;
                }

                this.formData.set('code', this.fields.code);
                apiCall.updateData('join_group', this.formData)
                .then(response =>{
                    if(response.data == false){
                        this.message = "No group found for that code";
                        this.$store.dispatch('getMessage', {message: this.message, color: 'red', time: 2000});
                        return;
                    }
                    this.$store.dispatch('resetToDefault');
                    this.message = "You have joined the group: ";
                    this.$store.dispatch('getMessage', {message: this.message});
                    this.formData =  new FormData();
                    this.$store.dispatch('getUserGroups');

                    this.$router.push({name: "home"});
                }).catch(error => {
                        this.errors = error;
                });
            },
        },

        mounted(){

        }
    }
</script>

<style scoped>

</style>