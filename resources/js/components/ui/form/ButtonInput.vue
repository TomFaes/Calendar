<template>
    <div class="form-group">
        <div class="row">
            <div class="col-lg-2 col-md-2"></div>
            <div class="col-lg-8 col-md-8">
                <button type="submit" :class="btnClass"><slot></slot></button>
            </div>
            <div class="col-lg-2 col-md-2"></div>
        </div>
    </div>
</template>

<script>
    export default {
         props: {
            'btnClass' : String
         }
    }
</script>
