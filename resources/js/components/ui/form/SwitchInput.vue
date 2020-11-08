<template>
    <div class="form-group">
         <global-layout :sizeForm="sizeForm">   
                   <label :for=inputName>{{ tekstLabel }}</label>
            <div class="custom-control custom-switch">
                <input  :disabled=disabled type="checkbox" class="custom-control-input" :name=inputName :id=inputId  :value="value" @input="$emit('input',  switchState)" @click="changeSwitch()" :checked="checked">
                <label class="custom-control-label" :for=inputName></label>
            </div>
        </global-layout>
    </div>
</template>

<script>
    export default {
         props: {
             'tekstLabel' : String,
            'inputName' : String,
            'inputId' : String,
            'errors' : Array,
            'value' : Boolean,
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

        watch:{
            'value'(){
                if(this.value == true){
                    this.switchState = true;
                    this.checked='checked';
                }
            }
         },

        methods: {
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

<style>

</style>

