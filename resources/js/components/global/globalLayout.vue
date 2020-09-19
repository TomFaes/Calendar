<template>
    <div class="row">
        <div :class="leftSite"></div>
        <div :class="middleSite">
            <slot></slot>
        </div>
        <div :class="rightSite"></div>
    </div>
</template>

<script>
    export default {
         props: {
            'sizeForm' : "",
         },

        data() {
            return {
                'leftSite': "col-lg-2 col-md-2 col-sm-0",
                'middleSite': "col-lg-8 col-md-8 col-sm-12",
                'rightSite': "col-lg-2 col-md-2 col-sm-0",
            }
        },

        methods: {
            setFormSize(){
                if(this.sizeForm == 'small'){
                    this.leftSite = "col-xl-4 col-lg-3 col-md-2 col-sm-0";
                    this.middleSite = "col-xl-4 col-lg-6 col-md-8 col-sm-12";
                    this.rightSite = "col-xl-4 col-lg-3 col-md-2 col-sm-0";
                }else if(this.sizeForm == 'large'){
                    this.leftSite = "col-xl-1 col-lg-1 col-md-0 col-sm-0";
                    this.middleSite = "col-xl-10 col-lg-10 col-md-12 col-sm-12";
                    this.rightSite = "col-xl-1 col-lg-1 col-md-0 col-sm-0";
                }else if(this.sizeForm == "xlarge"){
                    this.leftSite = "col-0";
                    this.middleSite = "col-12";
                    this.rightSite = "col-m-0";
                }
            },
        },

        mounted () {
            this.setFormSize();
        }
    }
</script>

<style scoped>

</style>
