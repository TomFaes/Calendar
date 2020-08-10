<template>
    <div>
        <div class="title">
            <slot name="title"></slot>
        </div>

        <div class="subtitle" v-if="$slots.features">Features</div>
        <span class="features" v-if="$slots.features">
            <slot name="features">
                None
            </slot>
        </span>

        <div class="subtitle"  v-if="$slots.stats">Extra stats options</div>
        <span class="stats"  v-if="$slots.stats">
            <slot name="stats">
                None
            </slot>
        </span>

        <div class="subtitle"  v-if="$slots.bugs">Fixed Bugs</div>
        <span class="bug"  v-if="$slots.features">
            <slot name="bugs">
                None
            </slot>
        </span>
    </div>
</template>

<script>

    export default {
        components: {

        },

        data (){
            return {

            }

        },

        props: {

         },

        methods: {

        },

        mounted () {

        }
    }

</script>
<style scoped>
    .title{
        display: block;
        font-size: 20px;
        font-weight: bold;
    }

    .subtitle{
        font-weight: bold;
        display: block;
        margin-left: 15px;
        margin-top: 20px;
    }

    .features, .stats, .bug {
        margin-left: 15px;
        display: block;
        margin-left: 20px;
        background-color: lightblue;
    }
</style>
