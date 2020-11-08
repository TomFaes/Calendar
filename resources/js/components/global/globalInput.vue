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

        <global-layout :sizeForm="sizeForm" v-if="type == 'switchButton'">
            <label :for=inputName>{{ tekstLabel }}</label>
            <div class="custom-control custom-switch">

                <input  :disabled=disabled type='checkbox' class="custom-control-input" :name=inputName :id=inputId  :value="value" @input="$emit('input',  switchState)" @click="changeSwitch()" :checked="checked">
                <label class="custom-control-label" :for=inputName></label>
            </div>
            <div class="text-danger" v-if="errors">{{ errors[0] }}</div>
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
            'value' : [String, Boolean],
            'boolValue': Boolean, 
            'disabled': Boolean,
            'sizeForm': String,
            'switchType': String,
         },

        data() {
            return {
                switchState: "false",
                checked: '',
            }
        },

        watch: {
            value: function (newValue) {
                if(this.type != "date" && this.type != 'switchButton'){
                    return "";
                }

                 if(this.type == "date"){
                     if(this.value == undefined || this.value == ""){
                        this.date = Moment().format('YYYY-MM-DD');
                    }else{
                        this.date = this.value;
                    }
                }

                if(this.type == "switchButton"){
                    if(this.value == true){
                        this.switchState = true;
                        this.checked='checked';
                    }
                }
            },
        },

        methods: {
            //switchButton
            changeSwitch(){
                if(this.switchState == true){
                    this.switchState = false;
                    this.checked='';
                }else{
                    this.switchState = true;
                    this.checked='checked';
                }
            }
        },

        mounted(){

        }
    }
</script>

<style scoped>

</style>

