<template>
    <div class="form-group">
       <global-layout :sizeForm="sizeForm">
            <label :for=inputName>{{ tekstLabel }}</label>
            <select :disabled=disabled v-model="selectedOption" class="form-control" :name=inputName :id=inputId  @input="$emit('input', $event.target.value)" >
                <option :value=0></option>
                <option v-for="(option) in options" :value="option.id" :key="option.id">{{ option.display }}</option>
            </select>
            <div class="text-danger" v-if="errors">{{ errors[0] }}</div>
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
            'value' : {String, Number},
            options: {
                type: Array,
            },
            'disabled': Boolean,
            'sizeForm': String,
         },

        data () {
            return {
                selectedOption: null
            }
        },

        mounted () {
            this.selectedOption = this.value
        },

        watch: {
            value: function (newValue) {
                this.selectedOption = newValue
            }
        }
    }
</script>
