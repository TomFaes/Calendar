<template>
    <div class="form-group">
        <global-layout :sizeForm="sizeForm">
            <label :for=inputName>{{ tekstLabel }}</label>
            <input :disabled=disabled type="date" class="form-control" :name=inputName :id=inputId  :value="date" @input="$emit('input', $event.target.value)"/>
            <div class="text-danger" v-if="errors">{{ errors[0] }}</div>
        </global-layout>
    </div>
</template>

<script>
    import Moment from 'moment';
    export default {
         props: {
             'tekstLabel' : String,
            'inputName' : String,
            'inputId' : String,
            'errors' : Array,
            'value' : String,
            'disabled': Boolean,
             'sizeForm': String,
         },
         data () {
            return {
                date: Moment().format('YYYY-MM-DD'),
            }
         },

         watch: {
            value: function (newValue) {
                if(this.value == undefined || this.value == ""){
                    this.date = Moment().format('YYYY-MM-DD');
                }else{
                    this.date = this.value;
                }
            }
        }
    }
</script>

<style>

</style>

