<template>
    <div>
        <div class="row">
            <h2>Groepen</h2>
        </div>
        <button class="btn btn-primary" @click.prevent="displayCreate" ><i class="fa fa-plus"></i></button>
        
        <div v-show="display == 'Create'">
            <inputForm  v-if="display == 'Create'" :submitOption="'Create'" ></inputForm>
        </div>
        
        <list></list>
    </div>
</template>

<script>
    import list from '../GroupPage/list.vue';
    import inputForm from '../GroupPage/input';

    export default {
        components: {
            list,
            inputForm,
        },

        data () {
            return {
                display: "",
            }
        },

        methods: {
            displayCreate(){
                if(this.display == 'Create'){
                    this.display = '';
                }else{
                    this.display = 'Create';
                }
            }
        },

        mounted(){
            
        }
    }
</script>

<style scoped>

</style>