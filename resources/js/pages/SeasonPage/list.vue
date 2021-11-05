<template>
    <div>
        <hr>
        <table class="table table-hover table-sm" v-if="seasons.data">
            <thead>
                <tr>
                    <th>Name</th>
                    <th  class="d-none d-sm-table-cell">Type</th>
                    <th>Periode</th>
                    <th>Wanneer</th>
                    <th class="d-none d-sm-table-cell">Public</th>
                    <th class="d-none d-sm-table-cell">Is generated</th>
                    <th class="d-none d-sm-table-cell">Admin</th>
                    <th class="d-none d-sm-table-cell">Groep</th>
                    <th>Options</th>                  
                </tr>
            </thead>
            <tbody  v-for="data in seasons.data"  :key="data.id" >
                    <tr>
                        <td>{{ data.name }}</td>
                        <td class="d-none d-sm-table-cell">{{ data.type }}</td>
                        <td>{{ convertDate(data.begin) }} tot  {{ convertDate(data.end) }}</td>
                        <td>{{ data.day }} {{ convertTime(data.start_hour) }}</td>
                        <td class="d-none d-sm-table-cell">{{ data.public ? 'Yes' : 'No'}}</td> 
                        <td class="d-none d-sm-table-cell">{{ data.is_generated ? 'Yes' : 'No'}}</td> 
                        <td class="d-none d-sm-table-cell">{{ data.admin.full_name }}</td>
                        <td class="d-none d-sm-table-cell">{{ data.group.name }}</td>
                        <td>
                            <button v-if="data.season_draw > 0" class="btn btn-primary" @click="navigation('calendar', data.id)" ><i class="fas fa-info-circle fa-1x"></i></button>
                            <button v-else class="btn btn-primary" @click="navigation('seasonDetail', data.id)"><i class="fas fa-info-circle fa-1x"></i></button>
                        </td>
                    </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import Moment from 'moment';
    import VuePagination from '../../components/ui/pagination.vue';

    import ButtonInput from '../../components/ui/form/ButtonInput.vue';

    export default {
        data () {
            return {
                
            }
        },

        components: {
            VuePagination,
            ButtonInput,
            Moment,
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
            
            navigation(name, id){
                if(this.$route.name == name){
                    return;
                }
                if(name == 'home'){
                    this.$store.dispatch('resetToDefault');
                }
                this.$router.push({name: name, params: { id: id },})
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
            this.$bus.$on('reloadSeasons', () => {
                this.loadList();
            });
        }
    }
</script>

<style scoped>

</style>