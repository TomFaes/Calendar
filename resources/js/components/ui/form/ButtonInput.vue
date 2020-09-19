<template>
    <div class="form-group">
        <global-layout :sizeForm="sizeForm">
            <button type="submit" :class="btnClass"><slot></slot></button>
        </global-layout>
    </div>
</template>

<script>
    export default {
         props: {
            'btnClass' : String,
            'sizeForm': String,
         }
    }
</script>
