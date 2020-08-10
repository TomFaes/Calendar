<template>
    <div class="form-group">
        <div class="row">
            <div class="col-lg-2 col-md-2"></div>
            <div class="col-lg-8 col-md-8">
                <label :for=inputName>{{ tekstLabel }}</label>
                <input :disabled=disabled type="date" class="form-control" :name=inputName :id=inputId  :value="date" @input="$emit('input', $event.target.value)"/>
                <div class="text-danger" v-if="errors">{{ errors[0] }}</div>
            </div>
            <div class="col-lg-2 col-md-2"></div>
        </div>
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
            'disabled': Boolean
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

