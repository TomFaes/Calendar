<template>
    <div>
        <hr>
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Firstname</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody  v-for="data in dataList.data"  :key="data.id" >
                <tr>
                    <td>{{ data.id }}</td>
                    <td>{{ data.firstname }}</td>
                    <td>{{ data.name }}</td>
                    <td>{{ data.email }}</td>
                    <td>{{ data.role }}</td>
                   <td class="options-column">
                        <button class="btn btn-primary"  v-if="loggedInUser.role == 'Admin'" @click.prevent="updateUser(data.id)"><i class="fa fa-edit" style="heigth:14px; width:14px"></i></button>
                        <button class="btn btn-danger" v-if="loggedInUser.role == 'Admin'" @click.prevent="deleteUser(data)" ><i class="fas fa-trash fa-1x" ></i></button>
                    </td>
                </tr>
                <tr v-if="selectedUserId == data.id">
                    <td colspan="100%">
                        <inputForm  v-if="selectedUserId == data.id" :user=data :submitOption="view"></inputForm>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <vue-pagination  :pagination="dataList" @paginate="loadList()" :offset="4"></vue-pagination>
        
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import VuePagination from '../../components/ui/pagination.vue';

    import ButtonInput from '../../components/ui/form/ButtonInput.vue';

    import inputForm from '../UserPage/input';

    export default {
        data () {
            return {
                'dataList' : {},
                'loggedInUser' : {},
                'selectedUserId': "",
                'view': "",
            }
        },

        components: {
            VuePagination,
            inputForm,
            ButtonInput
        },

        methods: {
            loadList(){
                apiCall.getData('user?page=' + this.dataList.current_page)
                .then(response =>{
                    this.dataList = response;
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },

            updateUser(id){
                if(this.selectedUserId == id && this.view == "Update"){
                    this.selectedUserId = id;
                    this.view = "";
                }else{
                    this.selectedUserId = id;
                    this.view = "Update";
                }
            },

            deleteUser(data){
                if(confirm('are you sure you want to remove ' + data.firstname + ' ' + data.name + '?')){
                    apiCall.postData('user/' + data.id + '/delete')
                    .then(response =>{
                        //console.log("delete user");
                        this.loadList();
                    }).catch(() => {
                        console.log('handle server error from here');
                    });
                }
            }
        },

        mounted(){
            this.loadList();
            this.$bus.$on('reloadUserList', () => {
                this.selectedUserId = "";
                this.loadList();
            });
            this.loggedInUser = this.$store.state.LoggedInUser
        }
    }
</script>

<style scoped>

</style>

