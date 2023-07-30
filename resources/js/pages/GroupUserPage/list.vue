<template>
    <div class="container">
        <h3>Group users</h3>

        <global-layout center="center">
            <button class="btn btn-primary" v-if="group.type_member == 'Admin'" @click.prevent="addGroupUser" > <i class="fa fa-user-plus"></i></button><br><br>
        </global-layout>

        <div v-show="display == 'showAddUserToGroup'">
            <addUserToGroup :submitOption="'Create'" :group=group></addUserToGroup>
        </div>

        <table class="table table-hover table-sm" v-if="groupUsers">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody  v-for="data in groupUsers"  :key="data.id" >
                    <tr>
                        <td>
                            {{ displayFullName(data) }}
                        </td>
                        <td v-if="group.type_member == 'Admin'">
                            <a class="mailtoui"
                                :href="createLink(data.code)"
                                v-if="data.code != null"
                                >{{data.code}}</a>
                    </td>
                        <td v-if="group.type_member == 'Admin'" class="options-column">
                            
                            <button class="btn btn-primary" @click.prevent="updateGroupUser(data)"> <i class="fa fa-edit" style="heigth:14px; width:14px"></i></button>
                            <button class="btn btn-secondary" v-if="!data.user_id" @click.prevent="regenerateCode(data)" ><i class="fas fa-sync" style="heigth:14px; width:14px" ></i></button>
                            <button class="btn btn-danger" @click.prevent="deleteGroupUser(data)" ><i class="fas fa-trash-alt" style="heigth:14px; width:14px" ></i></button>
                        </td>
                    </tr>
                    <tr v-show="display == 'showEditGroupUser' && selectedGroupUser.id == data.id ">
                        <td colspan="100%">
                            <add-user-to-group :group=group :groupUser=selectedGroupUser :submitOption="'Update'" :key=selectedGroupUser.id></add-user-to-group>
                        </td>
                    </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import addUserToGroup from '../GroupUserPage/input.vue';

    export default {
        data () {
            return {
                display: "",
                selectedGroupUser: 0,
            }
        },

        components: {
            addUserToGroup,
        },

        computed: {
            group(){
                return this.$store.state.selectedGroup;
            },

            user(){
                return this.$store.state.loggedInUser;
            },

            groupUsers(){
                if(this.group.id === undefined){
                    return;
                }

                if(this.$store.state.selectedGroupUsers.data == undefined){
                    this.$store.dispatch('getSelectedGroupUsers', {groupId: this.group.id});
                }
                return this.$store.state.selectedGroupUsers.data;
            },
        },

        methods: {
            displayFullName(data){
                if(data.user.firstname != null){
                    return data.user.full_name;
                }
                return data.full_name;
            }, 
            
            updateGroupUser(groupUser){
                if(this.selectedGroupUser.id == groupUser.id){
                    this.selectedGroupUser = 0;
                    this.display = '';
                }else{
                    this.selectedGroupUser = groupUser;
                    this.display = 'showEditGroupUser';
                }
            },

            addGroupUser(){
                if(this.display == 'showAddUserToGroup'){
                    this.display = '';
                }else{
                    this.display = 'showAddUserToGroup';
                }
            },

            regenerateCode(data){
                apiCall.postData('group/' + this.group.id + '/user/' + data.id + '/regenerate_code')
                    .then(response =>{
                        this.message = "Code is regenerated for " + data.full_name;
                        this.$store.dispatch('getMessage', {message: this.message});
                        this.$store.dispatch('getSelectedGroupUsers', {groupId: this.group.id});
                    }).catch(() => {
                        console.log('handle server error from here');
                    });
            },

            deleteGroupUser(data){
                if(confirm('are you sure you want to remove this user from ' + this.group.name  + '?')){
                    apiCall.postData('group/' + this.group.id + '/user/' + data.id + '/delete')
                    .then(response =>{
                        if(response.status == 403){
                            this.message = "This user couldn't be delete from this group";
                        }
                        else if(response.status == 202){
                            this.message = response.data;
                        }
                        else{
                            this.message = data.full_name + " is deleted from this group";
                        }
                        this.$store.dispatch('getMessage', {message: this.message, color: 'red', time: 2000});
                        this.$store.dispatch('getSelectedGroupUsers', {groupId: this.group.id});
                    }).catch(() => {
                        console.log('handle server error from here');
                    });
                }
            },

            createLink(code){
                var subject = "Your invitation code for the boardgametracker";
                subject = subject.replace(/\s/g, '%20');

                var body = "%0D%0A You have been invited to join " + this.group.name
                + ". After logging in you can enter this code to join the group: " + code;

                body = body.replace(/\s/g, '%20');

                var maillink = "mailto:?" +
                    "subject=" + subject +
                    "&body=" + body
                    ;
                return maillink;
            }
            
        },

        mounted(){
            
        }
        
    }
</script>

<style scoped>

</style>
