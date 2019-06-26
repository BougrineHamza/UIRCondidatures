<template>
    <div class="row pt-2 no-gutters">
        {{navigate_me}}
        <div class="col d-md-flex d-none">
            <div @click="gotostep(1)" class="step mr-3" :class="{'step-on':step_levels[0],'step-active':$route.name == 'step1'}" data-toggle="tooltip" title="Modifier mon Profil"><span>1</span></div>
            <div @click="gotostep(2)" class="step mr-3" :class="{'step-on':step_levels[1],'step-active': $route.name == 'step2'}" data-toggle="tooltip" title="Modifier mon Cursus"><span>2</span></div>
            <div @click="gotostep(3)" class="step mr-3" :class="{'step-on':step_levels[2],'step-active': $route.name == 'step3'}" data-toggle="tooltip" title="Choisir ma Formation"><span>3</span></div>
            <div @click="gotostep(4)" class="step mr-3" :class="{'step-on':step_levels[3],'step-active': $route.name == 'step4'}" data-toggle="tooltip" title="Choisir mes RDV"><span>4</span></div>
        </div>
    </div>
</template>

<script>

export default {
    props: [],

    data() {
        return {

        }
    },

    mounted: function() {
        if(this.$store.state.connected){
                this.$store.dispatch('tellSetApiToken');

                // On ramène de la DATA
                this.$store.dispatch('tellGetUserInfo', {router: this.$router});


                if(this.$route.name == 'step3' || this.$route.name == 'step4'){
                    this.$store.dispatch('tellGetDisposList');
                }

                if(this.$route.name == 'step4' || this.$route.name == 'mes-convocations'){
                    this.$store.dispatch('tellGetMyConvocations');
                }
        }

                this.$store.dispatch('tellGetSpecialitesList');
                this.$store.dispatch('tellPreloading',true);


    },

    methods: {
        gotostep: function(id){
            if(id <= this.lead_level + 1){

                if(id != 1 && id != 2){
                    this.$store.dispatch('tellPreloading',true);
                }

                if(id == 3 || id == 4){
                    this.$store.dispatch('tellGetDisposList');
                }

                this.$router.push({ name: 'step'+id });

            } else {
                this.$toasted.global.step_left();
            }
        }
    },
    computed: {
        navigate_me(){
            if(this.$store.state.connected){
                if(this.$route.name == 'step1'){
                    this.$store.dispatch('tellStepLevel', 1);
                } else if(this.$route.name == 'step2'){
                    this.$store.dispatch('tellStepLevel', 2);
                } else if(this.$route.name == 'step3'){
                    let obj = this;
                    setTimeout(function(){obj.$store.dispatch('tellGetFormationsList');},600);
                    this.$store.dispatch('tellStepLevel', 3);
                } else if(this.$route.name == 'step4'){
                    this.$store.dispatch('tellStepLevel', 4);
                } else if(this.$route.name == 'done'){
                    this.$store.dispatch('tellStepLevel', 5);
                } else if(this.$route.name == 'mes-convocations'){
                    this.$store.dispatch('tellStepLevel', 6);
                } else if(this.$route.name == 'params'){
                    this.$store.dispatch('tellStepLevel', 7);
                } else if(this.$route.name == 'support'){
                    this.$store.dispatch('tellStepLevel', 0);
                } else {
                    //this.$router.push({name:"step1"});
                }
            } else {
                if(this.$route.name == 'auth' || this.$route.name == 'connexion'){
                    this.$store.dispatch('tellStepLevel', 0);
                } else if(this.$route.name == 'mdp-oublie'){
                    this.$store.dispatch('tellStepLevel', 0);
                } else if(this.$route.name == 'support'){
                    this.$store.dispatch('tellStepLevel', 0);
                } else {
                    this.$router.push({name:"auth"});
                    this.$toasted.global.not_connected();
                }
            }

            return;
        },
        store_ApiToken(){
            return this.$store.state.api_token;
        },
        step_level() {
            return this.$store.state.step_level;
        },
        step_levels() {
            return this.$store.state.steps;
        },
        lead_level(){
            return this.$store.state.lead_level;
        }
    }

}

</script>
