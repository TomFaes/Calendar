<template>
    <div class="row" v-if="dataList.length > 0">
        <div class="col-lg-2 col-md-2 col-sm-0"></div>
        <div class="col-lg-8 col-md-8 col-sm-12">
            <center>
                Your account has been added to {{ dataList.length }} group(s)<br>
                    <ul v-for="data in dataList"  :key="data.id" style="display: inline-block; text-align: left;">
                        <li>
                            {{ data.group['name'] }}
                            <button class="btn btn-primary" @click.prevent="approveVerifyUser(data.id)"><i class="fas fa-check"></i></button>
                            <button class="btn btn-danger" @click.prevent="disapproveVerifyUser(data.id)"><i class="fas fa-trash-alt" style="heigth:14px; width:14px" ></i></button>
                        </li>
                    </ul>
            </center>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-0"></div>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';

    export default {
        components: {

        },

         data () {
            return {
                'dataList' : { },
            }
        },

        methods: {
            loadList(){
                
                apiCall.getData('unverified-group-user')
                .then(response =>{
                    this.dataList = response;
                    this.updateField = '';
                }).catch(() => {
                    console.log('loadList: handle server error from here');
                });
            },

            approveVerifyUser(id){
                apiCall.updateData('unverified-group-user/' + id)
                .then(response =>{
                    this.loadList();
                    this.$bus.$emit('reloadGroups');
                }).catch(() => {
                    console.log('approveVerifyUser: handle server error from here');
                });
            },

            disapproveVerifyUser(id){
                apiCall.postData('unverified-group-user/' + id + '/delete')
                .then(response =>{
                    this.loadList();
                }).catch(() => {
                    console.log('disapproveVerifyUser: handle server error from here');
                });
            }
        },

        mounted(){
            this.loadList();
        }
    }
</script>

<style scoped>

</style>
