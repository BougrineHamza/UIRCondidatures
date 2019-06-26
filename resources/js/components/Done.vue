<template>
    <div class="" style="height:90vh;">

    </div>
</template>
<script>
export default {
    props: [],

    data() {
        return {


        }
    },

    computed: {

    },

    mounted: function() {
        if(this.$store.state.step_level != 5){
            this.$router.push({name:"step4"});
        }
    },

    methods: {



    }

}

</script>
