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
            <tbody  v-for="data in dataList"  :key="data.id" >
                    <tr>
                        <td>{{ data.name }}</td>
                        <td  class="d-none d-sm-table-cell">{{ data.descritpion }}</td>
                        <td  class="d-none d-sm-table-cell">{{ data.admin.fullName }}</td>
                        <td nowrap>
                            <button v-if="user.id == data.admin_id" class="btn btn-primary" @click.prevent="editGroup(data.id)"><i class="fa fa-edit" style="heigth:14px; width:14px"></i></button>
                            <button v-if="user.id == data.admin_id" class="btn btn-danger" @click.prevent="deleteGroup(data)"><i class="fas fa-trash fa-1x" ></i></button>
                            <button class="btn btn-secondary" @click.prevent="listUsers(data)"><i class="fas fa-users  fa-1x"></i></button>
                        </td>
                    </tr>
                    <tr v-if="updateField == data.id">
                        <td colspan="100%">
                            <inputForm  v-if="updateField == data.id" :group=data :submitOption="'Update'"></inputForm>
                        </td>
                    </tr>
                     <tr v-if="selectedGroup.id == data.id">
                        <td colspan="100%">
                            <user-list :group=selectedGroup></user-list>
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
    import inputForm from '../GroupPage/input';
    import userList from '../GroupUserPage/list.vue';

    export default {
        data () {
            return {
                'dataList' : { },
                fields: [
                    { 'field': 'id'},
                    { 'field': 'name'},
                    { 'field': 'description'},
                    { 'field': 'admin_id'},
                ],
                'updateField' : 0,
                'selectedGroup': 0,
            }
        },

        components: {
            VuePagination,
            inputForm,
            ButtonInput,
            userList
        },

        methods: {
            loadList(){
                apiCall.getData('user-group')
                .then(response =>{
                    this.dataList = response;
                    this.updateField = '';
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },

            editGroup(id){
                if( this.updateField == id){
                    this.updateField = 0;
                }else{
                    this.updateField = id;
                }
            },

            listUsers(group){
                if(this.selectedGroup.id == group.id){
                    this.selectedGroup = 0;
                }else{
                    this.selectedGroup = group;
                }
            },

            deleteGroup(group){
                if(confirm('are you sure you want to delete this group ' + group.name + '?')){
                    apiCall.postData('group/' + group.id + '/delete')
                    .then(response =>{
                        this.$bus.$emit('showMessage', response,  'red', '2000' );
                        this.loadList();
                    }).catch(() => {
                        console.log('handle server error from here');
                    });
                }
            }
        },

        mounted(){
            this.loadList();
            this.$bus.$on('reloadGroupList', () => {
                this.loadList();
            });
            this.user =  this.$store.state.LoggedInUser;
        }
    }
</script>

<style scoped>

</style>
