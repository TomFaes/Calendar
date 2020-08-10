<template>
    <div class="form-group">
        <div class="row">
            <div class="col-lg-2 col-md-2"></div>
            <div class="col-lg-8 col-md-8">
                    <label :for=inputName>{{ tekstLabel }}</label>
                    <select :disabled=disabled v-model="selectedOption" class="form-control" :name=inputName :id=inputId  @input="$emit('input', $event.target.value)" >
                        <option :value=0></option>
                        <option v-for="(option) in options" :value="option.id" :key="option.id">{{ option.display }}</option>
                </select>
                <div class="text-danger" v-if="errors">{{ errors[0] }}</div>
            </div>
            <div class="col-lg-2 col-md-2"></div>
        </div>
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
            'disabled': Boolean
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
