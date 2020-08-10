<template>
    <div>
       <hr>
        User
        <hr>
        <button class="btn btn-primary" @click.prevent="CreateShow" v-show="display == ''">Create</button>
        <button class="btn btn-primary" @click.prevent="CreateHide" v-show="display == 'Create'">Hide create</button>
        <div v-show="display == 'Create'">
            <inputForm  v-if="display == 'Create'" :submitOption="'Create'" ></inputForm>
        </div>
        <list></list>
    </div>
</template>

<script>
    import list from '../UserPage/list.vue';
    import inputForm from '../UserPage/input';

    export default {
        components: {
            list,
            inputForm
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
            this.$bus.$on('resetUserDisplay', () => {
                    this.display = '';
            });
        }
    }
</script>

<style scoped>

</style>

