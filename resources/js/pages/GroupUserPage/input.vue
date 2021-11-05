<template>
    <div>
        <hr>
        <form @submit.prevent="submit" method="POST" enctype="multipart/form-data">
            <global-input type='text' inputName="firstname" inputId="firstname" tekstLabel="firstname: " v-model="fields.firstname" :errors="errors.firstname" :value='fields.firstname'></global-input>
            <global-input type='text'  inputName="name" inputId="name" tekstLabel="name: " v-model="fields.name" :errors="errors.name" :value='fields.name'></global-input>
            <br>
            <global-layout>
                <center>
                    <button-input btnClass="btn btn-primary">Save user</button-input>
                </center>
            </global-layout>
        </form>
        <hr>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';

    import TextInput from '../../components/ui/form/TextInput.vue';
    import ButtonInput from '../../components/ui/form/ButtonInput.vue';

    export default {
        components: {
            TextInput,
            ButtonInput,
        },

         data () {
            return {
                'fields' : {
                    firstname: '',
                    name: '',
                },
                'errors' : [],
                'formData': new FormData(),
            }
        },

        props: {
            'groupUser': {},
            'group': {},
            'submitOption': ""
         },

        methods: {
            setFormData(){
                this.formData.set('firstname', this.fields.firstname ?? null);
                this.formData.set('name', this.fields.name ?? null);
                this.formData.set('group_id', this.group.id ?? null);
            },

            submit(){
                if(this.submitOption == 'Create'){
                    this.create();
                }else if(this.submitOption == 'Update'){
                    this.update();
                }else{
                    console.log('geen optie opgegeven');
                }
            },

            create(){
                this.setFormData();
                var action = 'group/' + this.group.id + '/user';

                apiCall.postData(action, this.formData)
                .then(response =>{
                    this.message = "You've added " + this.fields.firstname + " " + this.fields.name + " to " + this.group.name;
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    this.$store.dispatch('getSelectedGroupUsers', {groupId: this.group.id});
                    this.formData =  new FormData();
                    this.$bus.$emit('resetInput');
                    this.fields = {}; //Clear input fields.
                }).catch(error => {
                    this.errors = error;
                });
            },

            update(){
                this.setFormData();
                var action = 'group/' + this.group.id + '/user/' + this.groupUser.id;

                apiCall.updateData(action, this.formData)
                .then(response =>{
                    this.message = "You've updated the user " + this.fields.firstname + " " + this.fields.name + " for " + this.group.name;
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    this.$store.dispatch('getSelectedGroupUsers', {groupId: this.group.id});
                    this.formData =  new FormData();
                    this.$bus.$emit('resetInput');
                }).catch(error => {
                        this.errors = error;
                });
            },

            setData(){
                this.fields.firstname = this.groupUser.firstname;
                this.fields.name = this.groupUser.name;
                this.fields.email = this.groupUser.email;
                this.fields.group_id = this.groupUser.group_id;
            }
        },

        mounted(){
            if(this.groupUser != undefined){
                this.setData();
            }
        }
    }
</script>

<style scoped>

</style>
