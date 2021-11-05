<template>
    <div>
        <hr>
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Groep</th>
                    <th class="d-none d-sm-table-cell">Omschrijving</th>
                    <th class="d-none d-sm-table-cell">Admin</th>
                    <th>Opties</th>
                </tr>
            </thead>
            <tbody  v-for="data in dataList.data"  :key="data.id" >
                    <tr>
                        <td>{{ data.name }}</td>
                        <td  class="d-none d-sm-table-cell">{{ data.descritpion }}</td>
                        <td  class="d-none d-sm-table-cell">{{ data.admin.full_name }}</td>
                        <td nowrap>
                            <button class="btn btn-primary" @click="navigation('groupUsers', data.id)"><i class="fas fa-info-circle fa-1x"></i></button>
                        </td>
                    </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import VuePagination from '../../components/ui/pagination.vue';

    import ButtonInput from '../../components/ui/form/ButtonInput.vue';

    export default {
        data () {
            return {
                fields: [
                    { 'field': 'id'},
                    { 'field': 'name'},
                    { 'field': 'description'},
                    { 'field': 'admin_id'},
                ],
                'updateField' : 0,
            }
        },

        components: {
            VuePagination,
            ButtonInput,
        },

        computed: {
            user(){
                return this.$store.state.loggedInUser;
            },
            dataList(){
                return this.$store.state.userGroups;
            },
        },

        methods: {
            loadList(){
                this.$store.dispatch('getUserGroups');
                this.editGroup(0);
            },

            editGroup(id){
                if( this.updateField == id){
                    this.updateField = 0;
                }else{
                    this.updateField = id;
                }
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
        },

        mounted(){
            this.loadList();
            this.$bus.$on('reloadGroupList', () => {
                this.loadList();
            });
        }
    }
</script>

<style scoped>

</style>