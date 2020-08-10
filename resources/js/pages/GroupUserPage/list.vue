<template>
    <div class="container">
        <center>
            <button class="btn btn-primary" v-if="group.typeMember == 'Admin'" @click.prevent="addGroupUser" > <i class="fa fa-user-plus"></i></button><br><br>
        </center>

        <!-- Add user input -->
        <div v-show="display == 'showAddUserToGroup'">
            <addUserToGroup :submitOption="'Create'" :group=group></addUserToGroup>
        </div>

        <!-- list of all users -->
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody  v-for="data in groupUsers"  :key="data.id" >
                    <tr>
                        <td>
                            <div v-if="data.user_id > 0 && data.user.verified == 1">
                                {{ data.user.firstname }} {{ data.user.name }}
                            </div>
                            <div v-else>
                                {{data.firstname}} {{data.name}}
                            </div>
                        </td>
                        <td v-if="group.typeMember == 'Admin'" class="options-column">
                            <button class="btn btn-primary"  v-if="!data.user_id" @click.prevent="updateGroupUser(data)"> <i class="fa fa-edit" style="heigth:14px; width:14px"></i></button>
                            <button class="btn btn-danger" @click.prevent="deleteGroupUser(data)" ><i class="fas fa-trash-alt" style="heigth:14px; width:14px" ></i></button>
                        </td>
                    </tr>
                    <tr v-show="display == 'showEditGroupUser' && selectedGroupUser.id == data.id ">
                        <addUserToGroup :group=group :groupUser=selectedGroupUser :submitOption="'Update'" :key=selectedGroupUser.id></addUserToGroup>
                    </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import addUserToGroup from '../GroupUserPage/input';
    import ButtonInput from '../../components/ui/form/ButtonInput.vue';

    export default {
        data () {
            return {
                display: "",
                selectedGroupUser: 0,
                'groupUsers': {},
            }
        },

        props: {
            'group': {},
         },

        components: {
            addUserToGroup,
            ButtonInput
        },

        watch:{
            'group'(){
                this.setGroupUsers();
            }
         },

        methods: {
            setGroupUsers(){
                this.groupUsers = this.group.group_users;
                this.display = "";
            },

            getGroupUsers(groupId){
                apiCall.getData( 'group/' + groupId + '/user')
                .then(response =>{
                    this.selectedGroupUser = 0;
                    this.groupUsers = response;
                }).catch(() => {
                    console.log('handle server error from here');
                });
            },

            updateGroupUser(groupUser){
                if(this.display == 'showEditGroupUser'){
                    if(this.selectedGroupUser.id == groupUser.id){
                        this.display = '';
                    }else if(this.selectedGroupUser.id != groupUser.id){
                        this.selectedGroupUser = groupUser;
                    }else{
                        this.display = '';
                    }
                }else{
                    this.display = 'showEditGroupUser';
                    this.selectedGroupUser = groupUser;
                }
            },

            addGroupUser(){
                if(this.display == 'showAddUserToGroup'){
                    this.display = '';
                }else{
                    this.display = 'showAddUserToGroup';
                }
            },

            deleteGroupUser(data){
                if(confirm('are you sure you want to remove this user from ' + this.group.name  + '?')){
                    apiCall.postData('group/' + this.group.id + '/user/' + data.id + '/delete')
                    .then(response =>{
                        if(response.status == 204){
                            this.message = "This user couldn't be delete from this group";
                        }else{
                            this.message = data.fullName + " is deleted from this group";
                            this.getGroupUsers(this.group.id);
                        }
                        this.$bus.$emit('showMessage', this.message,  'red', '2000' );
                    }).catch(() => {
                        console.log('handle server error from here');
                    });
                }
            }
        },

        mounted(){
            this.setGroupUsers();
            this.$bus.$on('reloadList', () => {
                    this.display = "";
            });

            this.$bus.$on('reloadGroupUserList', (groupId) => {
                   this.getGroupUsers(groupId);
            });
        }
    }
</script>

<style scoped>

</style>