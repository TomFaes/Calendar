<template>
    <div>
        <div class="row">
            <h2>Groepen</h2>
        </div>
        <button class="btn btn-primary" @click.prevent="CreateShow" v-show="display == ''"><i class="fa fa-plus"></i></button>
        <button class="btn btn-primary" @click.prevent="CreateHide" v-show="display == 'Create'"><i class="fa fa-plus"></i></button><br><br>
        
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
            CreateShow(){
                this.display = 'Create';
            },

            CreateHide(){
                this.display = '';
            }
        },

        mounted(){
            this.$bus.$on('resetGroupDisplay', () => {
                    this.display = '';
            });
        }
    }
</script>

<style scoped>

</style>
