<template>
    <div :style="messageBarStyle" v-show="messageBarVisible" v-if="displayMessage" class="">
        {{ displayMessage }}
    </div>
</template>

<script>
    export default {
        components: {

        },

         data () {
            return {
                'displayMessage': '',
                'displayColor': "green",
                'displayTime': "2000",
                'messageBarVisible': true,
            }
        },

        props: {
            'message': '',

         },

         watch: {
             message(){
                 this.showMessage();
             }
         },

        methods: {
            showMessage(){
                this.messageBarVisible = true;
                this.displayMessage = this.message.message;
                this.displayColor = this.message.color;
                this.displayTime = this.message.time;
                setTimeout(() => this.messageBarVisible = false, this.displayTime);
            }
        },

        computed: {
            messageBarStyle(){
                return {
                    "padding": "10px",
                    "text-align": "center",
                    "color": "white",
                    "background-color": this.displayColor
                }
            }
        },

        mounted(){
           
        }
    }
</script>

<style scoped>

</style>
