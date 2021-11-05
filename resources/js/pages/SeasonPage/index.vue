<template>
    <div>
        <div class="row">
            <h2>Seasons</h2>
        </div>

        <button class="btn btn-primary" @click.prevent="displayCreate" ><i class="fa fa-plus"></i></button>
        <div v-show="display == 'Create'">
            <input-form  v-if="display == 'Create'" :submitOption="'Create'" ></input-form>
        </div>
         
        <list></list>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import inputForm from '../SeasonPage/input.vue';
    import list from '../SeasonPage/list.vue';
    import Moment from 'moment';

    export default {
        data () {
            return {
                'display': '',
            }
        },

        components: {
            Moment,
            list,
            inputForm

        },

        computed: {
            user(){
                return this.$store.state.loggedInUser;
            },
            
            seasons(){
                return this.$store.state.userSeasons;
            },
        },

        methods: {
            loadList(){
                this.$store.dispatch('getUserSeasons');
            },

            displayCreate(){
                if(this.display == 'Create'){
                    this.display = '';
                    return;
                }
                this.display = 'Create';
            },
            
            convertDate(value){
                return Moment(value, "YYYY-MM-DD").format('DD/MM/YYYY');
            },

            convertTime(value){
                return Moment(value, "HH:mm:ss").format('HH:mm');
            },
        },

        mounted(){
            this.loadList();
            /*
            this.loadList();
            this.$bus.$on('reloadSeasons', () => {
                this.loadList();
            });
            this.user =  this.$store.state.LoggedInUser;
            */
        }
    }
</script>


<style scoped>
.absenceButton {
    margin: 2px;
}
</style>
