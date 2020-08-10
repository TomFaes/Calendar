<template>
    <div>
        <div class="row">
            <h2>Seizoenen</h2>
        </div>
            <button class="btn btn-primary" @click.prevent="CreateShow"><i class="fas fa-plus fa-1x" ></i></button><br>

        <div class="row" v-show="display == 'Create'">
            <div class="container">
                <inputForm  v-if="display == 'Create'" :submitOption="'Create'" :userGroups=userGroups ></inputForm>
            </div>
        </div>
        <list></list>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import list from '../SeasonPage/list.vue';
    import inputForm from '../SeasonPage/input.vue';

    export default {
        components: {
            list,
            inputForm,
        },

        data () {
            return {
                display: "",
                userGroups: {},
            }
        },

        methods: {
            CreateShow(){
                if(this.display == 'Create'){
                    this.display = "";
                }else{
                    this.display = 'Create';
                }
            },

            loadGroupList(){
                apiCall.getData('user-group')
                .then(response =>{
                    this.userGroups = response;
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },
        },

        mounted(){
            this.loadGroupList();
            this.$bus.$on('resetSeasonDisplay', () => {
                    this.display = '';
            });
        }
    }
</script>

<style scoped>

</style>
