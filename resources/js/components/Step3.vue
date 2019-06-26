<template>
    <div class="mt-5 w-100">
        <h4 class="sous_bonjour text-center mb-4">Quelles formations UIR vous intéressent ?</h4>
        <div class="d-flex justify-content-center align-items-center pt-5" v-if="preloading">
            <svg class="spinner mt-5" width="40px" height="40px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                <circle class="path_green" fill="none" stroke-width="8" stroke-linecap="round" cx="33" cy="33" r="23"></circle>
            </svg>
        </div>
        <div class=" w-100 row no-gutters" v-else>
            <div class="w-100">
                <div class="text-center col-md-10 offset-md-1">
                    <ul class="list-unstyle p-0">
                        <li v-for="forma in store_formations" class="formation d-flex align-items-center position-relative text-left py-3 pr-5" :class="myformations.indexOf(forma.id) >= 0 ? 'forma_checked' : ''" v-on="myformations.indexOf(forma.id) >= 0 ? {click: () => remove_formation(forma.id) } : {click: () => add_formation(forma.id) }">
                            <div class="logo_formation mr-md-4 mr-2"><img :src="'/images/'+forma.miniature" /></div>
                            <div class="titre_formation w-100">{{ forma.titre }}</div>
                            <div class="formation_btn">
                                <i class="material-icons" :class="myformations.indexOf(forma.id) >= 0  ? 'remove_formation' : 'add_formation'">check_circle</i>
                            </div>
                        </li>
                    </ul>
                    <div class="d-flex justify-content-between align-items-center">
                        <div @click="prevstep()" class="goback d-flex align-items-center"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="35.645px" height="8.175px" viewBox="0 0 35.645 8.175" enable-background="new 0 0 35.645 8.175" xml:space="preserve">
                                <g>
                                    <path d="M34.83,3.455H1.913l2.499-2.499c0.219-0.219,0.219-0.574,0-0.792c-0.219-0.219-0.574-0.219-0.792,0L0.164,3.619
        c-0.219,0.219-0.219,0.574,0,0.792l3.455,3.455c0.109,0.109,0.253,0.164,0.396,0.164c0.143,0,0.287-0.055,0.396-0.164
        c0.219-0.219,0.219-0.574,0-0.792L1.913,4.576H34.83c0.309,0,0.56-0.251,0.56-0.56C35.39,3.706,35.139,3.455,34.83,3.455z" />
                                </g>
                            </svg>
                        </div>
                        <button class="btn" :class="myformations.length > 0 ? 'btn-primary' : 'btn-secondary'" type="button" @click="myformations.length > 0 ? nextstep() : toast()">Suivant</button>
                    </div>
                </div>
                <div class="modal left fade" tabindex="-1" role="dialog" id="conditions">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-uppercase">Prérequis de la formation</h5>
                                <button type="button" @click="removed()" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-08">
                                <div v-html="prerequis.contenu"></div>
                            </div>
                            <div class="pr-3 small custom-control custom-checkbox text-right position-relative mb-2">

                                <input v-model="oui_prerequis_model" type="checkbox" class="custom-control-input" id="oui_prerequisid" name="oui_prerequis" v-validate="'required'" :class="{'haserror': errors.has('form-4.oui_prerequis') }" data-vv-as="Prérequis" data-vv-scope="form-4">

                                <small v-show="errors.has('form-4.oui_prerequis')" class="help" style="right:0 !important;">{{ errors.first('form-4.oui_prerequis') }}</small>

                                <label class="custom-control-label cndp text-muted" for="oui_prerequisid">Je confirme avoir pris connaissance des prérequis</label>

                            </div>
                            <div class="modal-footer">
                                <button type="button" @click="removed()" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                <button type="button" class="btn" :class="oui_prerequis_model ? 'btn-primary' : 'btn-secondary'" @click="!oui_prerequis_model ? prerequis_check('form-4') : prerequis_good()">Oui, choisir</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: [],

    data() {
        return {
            myformations: [],
            prerequis: '',
            id: null,
            oui_prerequis_model: false

        }
    },

    computed: {
        preloading() {
            return this.$store.state.preloading;
        },
        store_formations() {
            if (this.$store.state.formations_choisies.length > 0) {
                this.myformations = this.$store.state.formations_choisies;
            }

            if (this.$store.state.cursus.niveau != null) {
                return this.$store.getters['getFormations'](Number(this.$store.state.cursus.niveau.slice(-1)));
            } else { return null }

        }
    },

    mounted: function() {

    },

    methods: {
        toast: function() {
            this.$toasted.show('Veuillez choisir une formation...', {
                icon: 'do_not_disturb',
                duration: 2500,
                closeOnSwipe: true
            });
        },
        getprerequis: function(formation_id) {
            this.prerequis = this.$store.getters['getPrerequis'](formation_id);
        },
        remove_formation: function(id) {
            this.myformations = this.myformations.filter(function(value, index, arr) {
                return value != id;
            });
        },

        add_formation: function(id) {
            this.getprerequis(id);
            setTimeout(function() { $('#conditions').modal({ backdrop: 'static', keyboard: false }) }, 0);
            this.id = id;
            this.myformations.push(id);
        },
        removed: function() {
            this.remove_formation(this.id);
        },
        prevstep: function() {
            this.$router.push({ name: 'step2' });
        },
        prerequis_check: function(scope){
             this.$validator.validateAll(scope).then(error => {

              });

        },
        prerequis_good: function(){
            this.oui_prerequis_model = null;
            this.$validator.reset();
            $('#conditions').modal('hide');
        },
        nextstep: function(scope) {
            this.$validator.validateAll(scope).then((result) => {
                if (result) {
                    this.$store.dispatch('tellPreloading', true);
                    this.$store.dispatch('tellStepDone', 2);
                    this.$store.dispatch('tellSaveMyFormations', this.myformations);
                    this.$toasted.global.saved();
                    this.$router.push({ name: 'step4' });
                    this.$store.dispatch('tellGetDisposList');
                }
            });
        },


    }

}

</script>
