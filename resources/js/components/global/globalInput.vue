<template>
    <div class="form-group">
        <global-layout :sizeForm="sizeForm" v-if="type == 'date' || type=='time'">
            <div class="row">
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-8">
                    <label :for=inputName>{{ tekstLabel }}</label>
                    <input :disabled=disabled :type=type class="form-control" :name=inputName :id=inputId  :value="value" @input="$emit('input', $event.target.value)"/>
                    <div class="text-danger" v-if="errors">{{ errors[0] }}</div>
                </div>
            </div>
        </global-layout>
        <global-layout :sizeForm="sizeForm" v-else>
            <label :for=inputName>{{ tekstLabel }}</label>
            <input :disabled=disabled :type=type class="form-control" :name=inputName :id=inputId  :value="value" @input="$emit('input', $event.target.value)"/>
            <div class="text-danger" v-if="errors">{{ errors[0] }}</div>
        </global-layout>
    </div>
</template>

<script>
    import Moment from 'moment';
    export default {
         props: {
            'type': String,
            'tekstLabel' : String,
            'inputName' : String,
            'inputId' : String,
            'errors' : Array,
            'value' : String,
            'disabled': Boolean,
            'sizeForm': String,
         },

        data() {
            return {

            }
        },

        watch: {
            value: function (newValue) {
                if(this.type != "date"){
                    return "";
                }
                if(this.value == undefined || this.value == ""){
                    this.date = Moment().format('YYYY-MM-DD');
                }else{
                    this.date = this.value;
                }
            }
        },

        mounted(){

        }
    }
</script>

<style scoped>

</style>

