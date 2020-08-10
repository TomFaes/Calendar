<template>
    <div :style="messageBarStyle" v-show="messageBarVisible" v-if="displayMessage" class="position-fixed col-8">
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
                'displayTime': "5000",
                'messageBarVisible': true,
            }
        },

        props: {

         },

        methods: {
            showMessage(message, color, time){
                this.messageBarVisible = true;
                this.displayMessage = message;
                this.displayColor = color;
                setTimeout(() => this.messageBarVisible = false, time)
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
            this.$bus.$on('showMessage', (message, color='green', time = '5000') => {
                this.showMessage(message, color, time);
            });
        }
    }
</script>

<style scoped>

</style>
