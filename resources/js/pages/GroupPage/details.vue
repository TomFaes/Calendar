<template>
    <div>
        <h1>{{  group.name}}</h1>
        <div class="button-row">
            <button class="btn btn-primary" @click="navigation('group')"><i class="fa fa-home fa-1x" ></i></button>
            <button class="btn btn-primary" @click="navigation('editGroup')" v-if="group.type_member == 'Admin'"><i class="fas fa-pencil-alt fa-1x" ></i></button>
            <button class="btn btn-secondary" @click="navigation('groupUsers')"><i class="fas fa-users fa-1x" ></i></button>
            <button v-if="group.type_member == 'Admin'" class="btn btn-danger" @click.prevent="deleteGroup()"><i class="fas fa-trash fa-1x" ></i></button>
        </div>
        <router-view v-if="group != '' " name="groupDetails" :group=group></router-view>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';

    export default {
        components: {

        },

        data () {
            return {

            }
        },

        props: {
            'id': '',
         },

         computed: {
            group(){
                return this.$store.state.selectedGroup;
            },

            user(){
                return this.$store.state.loggedInUser;
            }
        },

        methods: {
           getGroup(){
               if(this.group.id == this.id){
                   return;
               }
               this.$store.dispatch('getSelectedGroup', {id: this.id});
               return;
           },

           navigation(name){
                if(this.$route.name == name){
                    return;
                }
                if(name == 'group'){
                    this.$store.dispatch('resetToDefault');
                }
                this.$router.push({name: name, params: { id: this.id },})
           },

           deleteGroup(){
                if(confirm('are you sure you want to delete this group ' + this.group.name + '?')){
                    apiCall.postData('group/' + this.group.id + '/delete')
                    .then(response =>{                        
                        this.$store.dispatch('getMessage', {message: response.data, color: 'red', time: 2000});
                        this.$store.dispatch('resetToDefault');
                        this.$store.dispatch('getUserGroups');
                        this.$router.push({name: 'group', })
                    }).catch(() => {
                        console.log('handle server error from here');
                    });
                }
            },
        },

        mounted(){
            this.getGroup();
        }
    }
</script>

<style scoped>
    button {
        margin: 3px;
    }

    .button-row{
        width: 100%;
        text-align: center;
    }
</style>