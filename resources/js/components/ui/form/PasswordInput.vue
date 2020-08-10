<template>
    <div class="form-group">
        <div class="row">
            <div class="col-lg-2 col-md-2"></div>
            <div class="col-lg-8 col-md-8">
                <label :for=inputName>{{ tekstLabel }}</label>
                <input :disabled=disabled type="password" class="form-control" :name=inputName :id=inputId  :value="value" @input="$emit('input', $event.target.value)"/>
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
            'value' : String,
            'disabled': Boolean
         },

        data() {
            return {

            }
        }
    }
</script>
