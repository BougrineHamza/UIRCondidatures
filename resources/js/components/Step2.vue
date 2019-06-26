<template><div>
    <div class="d-flex justify-content-center align-items-center pt-5" v-if="preloading">
        <svg class="spinner mt-5" width="40px" height="40px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
            <circle class="path_green" fill="none" stroke-width="8" stroke-linecap="round" cx="33" cy="33" r="23"></circle>

        </svg>
    </div>
    <div class="mt-5" data-vv-scope="form-3" v-else>
        <h4 class="sous_bonjour" @click="showme()">Parlez-nous de votre cursus...</h4>
        <div style="width:320px;" class="text-center mt-4">
            <div class="mb-2">
                <div class="text-left form">
                    <div class="mb-2">
                        <div class="row form">
                            <div class="col text-left position-relative">
                                <label class="col-form-label" for="niveau">Niveau d'études</label>
                            </div>
                            <div class="col text-left position-relative">
                                <label class="col-form-label" for="annee">Année du diplôme</label>
                            </div>
                        </div>
                        <div class="position-relative ">
                            <div class="input-separator d-flex">

                                <div class="w-50">
                                <cool-select class="lefty" v-model="store_cursus.niveau" :items="getniveaux" name="niveau" v-validate="'required'" :class="{'haserror': errors.has('niveau') }" data-vv-as="Niveau" :disabled="dis_niveau" />
                                </div>
                                <div class="w-50">
                                <cool-select  class="righty" v-model="store_cursus.annee_diplome" :items="store_years" name="annee" v-validate="'required'" :class="{'haserror': errors.has('annee') }" data-vv-as="Année diplôme" :disabled="dis_annee_diplome" />
                                </div>

                            </div>
                            <small v-show="errors.has('niveau') || errors.has('annee')" class="help">
                                {{ errors.first('niveau') }} <br v-if="errors.has('niveau') && errors.has('annee')" />
                                {{ errors.first('annee') }}
                            </small>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row form">
                            <div class="col text-left position-relative">
                                <label class="col-form-label" for="prenom">Pays d'étude</label>
                            </div>
                            <div class="col text-left position-relative">
                                <label class="col-form-label" for="nom">Ville</label>
                            </div>
                        </div>
                        <div class="position-relative ">
                            <div class="input-separator d-flex">
                                <cool-select :ItemsLimit="mylimit" class="lefty" v-model="store_cursus.country_etudes" :items="store_countries" name="pays" v-validate="'required'" :class="{'haserror': errors.has('pays') }" data-vv-as="Pays" :disabled="dis_country_etudes"><template slot="no-data">Aucune donnée...</template></cool-select>

                                <cool-select :ItemsLimit="mylimit" class="righty" v-model="store_cursus.city_etudes" :items="store_cities" name="ville" v-validate="'required'" :class="{'haserror': errors.has('nom') }" data-vv-as="Ville" :disabled="dis_city_etudes" ><template slot="no-data">Aucune donnée...</template></cool-select>
                            </div>
                            <small v-show="errors.has('pays') || errors.has('ville')" class="help">
                                {{ errors.first('pays') }} <br v-if="errors.has('pays') && errors.has('ville')" />
                                {{ errors.first('ville') }}
                            </small>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row form">
                            <div class="col text-left position-relative">
                                <label class="col-form-label" for="systeme">Système d'étude</label>
                            </div>
                            <div class="col text-left position-relative">
                                <label class="col-form-label" for="institut">Type institut</label>
                            </div>
                        </div>
                        <div class="position-relative ">
                            <div class="input-separator d-flex">
                                <cool-select class="lefty" v-model="store_cursus.systeme_etudes" :items="store_nationalites" name="systeme" v-validate="'required'" :class="{'haserror': errors.has('systeme') }" data-vv-as="Système" :disabled="dis_systeme_etudes" />

                                <cool-select class="righty" v-model="store_cursus.type_institut" :items="getinstituts" name="institut" v-validate="'required'" :class="{'haserror': errors.has('nom') }" data-vv-as="Type institut" :disabled="dis_type_institut" />
                            </div>
                            <small v-show="errors.has('systeme') || errors.has('institut')" class="help">
                                {{ errors.first('systeme') }} <br v-if="errors.has('systeme') && errors.has('institut')" />
                                {{ errors.first('institut') }}
                            </small>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row form">
                            <div class="col text-left position-relative">
                                <label class="col-form-label" for="specialisation">Spécialisation</label>
                            </div>
                        </div>
                        <div class="position-relative ">
                            <cool-select class="rounded-input" v-model="store_cursus.specialite" :items="store_specialites" name="specialisation" v-validate="'required'" :class="{'haserror': errors.has('specialisation') }" data-vv-as="Spécialisation" :disabled="dis_specialites" />
                            <small v-show="errors.has('specialisation')" class="help">{{ errors.first('specialisation') }}
                            </small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row form">
                            <div class="col text-left position-relative">
                                <label class="col-form-label" for="etablissement">Etablissement</label>
                            </div>
                        </div>
                        <div class="position-relative etablissement">
                            <input @keypress.enter="nextstep()" type="text" class="form-control" v-model="store_cursus.etablissment" name="etablissement" v-validate="'required|max:50'" :class="{'haserror': errors.has('etablissement') }" data-vv-as="Etablissement" :disabled="dis_etablissement" />
                            <small v-show="errors.has('etablissement')" class="help">{{ errors.first('etablissement') }}
                            </small>
                        </div>
                    </div>
            {{store_disabled}}
                    <div class="d-flex justify-content-between align-items-center">
                        <div @click="prevstep()" class="goback d-flex align-items-center"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
     width="35.645px" height="8.175px" viewBox="0 0 35.645 8.175" enable-background="new 0 0 35.645 8.175" xml:space="preserve">
<g>
    <path d="M34.83,3.455H1.913l2.499-2.499c0.219-0.219,0.219-0.574,0-0.792c-0.219-0.219-0.574-0.219-0.792,0L0.164,3.619
        c-0.219,0.219-0.219,0.574,0,0.792l3.455,3.455c0.109,0.109,0.253,0.164,0.396,0.164c0.143,0,0.287-0.055,0.396-0.164
        c0.219-0.219,0.219-0.574,0-0.792L1.913,4.576H34.83c0.309,0,0.56-0.251,0.56-0.56C35.39,3.706,35.139,3.455,34.83,3.455z"/>
</g>
</svg>

</div>
                        <button class="btn btn-primary" type="button" @click="nextstep()">Suivant</button>
                    </div>
                </div>
            </div>
        </div>
    </div></div>
</template>
<script>
import { CoolSelect } from 'vue-cool-select';
export default {
    components: { CoolSelect },

    props: [],

    data() {
        return {
            getniveaux: ['Bac+1', 'Bac+2', 'Bac+3', 'Bac+4', 'Bac+5'],
            getinstituts: ['Privé', 'Public'],
            dis_niveau: false,
            dis_annee_diplome:false,
            dis_country_etudes: false,
            dis_city_etudes: false,
            dis_systeme_etudes: false,
            dis_type_institut: false,
            dis_specialites: false,
            dis_etablissement: false,
            mylimit: 100


        }
    },
    computed: {
        store_countries() {
            return this.$store.getters.getCountries.sort(function (a, b) {
              if(a == b) return 0;
              if (a == 'Maroc') return -1;
              if (b == 'Maroc') return 1;


              if (a == 'Autre') return 1;
              if (b == 'Autre') return -1;

              if (a < b)
                  return -1;
              if (a > b)
                  return 1;
              return 0;
            });
        },
        store_cities() {
            if (this.store_cursus.country_etudes) {
                return this.$store.getters['getCities'](this.store_cursus.country_etudes).cities.map(item => item.titre).sort(function (a, b) {
              if(a == b) return 0;
              if (a == 'Autre') return 1;
              if (b == 'Autre') return -1;

              if (a < b)
                  return -1;
              if (a > b)
                  return 1;
              return 0;
            });
            } else {
                return ['-Choisir Pays-'];
            }
        },
        store_nationalites() {
            return this.$store.getters.getNationalites.sort(function (a, b) {
              if(a == b) return 0;
              if (a == 'Autre') return 1;
              if (b == 'Autre') return -1;

              if (a < b)
                  return -1;
              if (a > b)
                  return 1;
              return 0;
            });
        },

        store_disabled(){
            this.dis_niveau = this.$store.state.disabled.niveau;
            this.dis_annee_diplome = this.$store.state.disabled.annee_diplome;
            this.dis_country_etudes = this.$store.state.disabled.country_etudes;
            this.dis_city_etudes = this.$store.state.disabled.city_etudes;
            this.dis_systeme_etudes = this.$store.state.disabled.systeme_etudes;
            this.dis_type_institut = this.$store.state.disabled.type_institut;
            this.dis_specialites = this.$store.state.disabled.specialites;
            this.dis_etablissement = this.$store.state.disabled.etablissement;

            return console.log('Champs désactivés');
        },
        store_specialites() {
            return this.$store.getters['getSpecialites'](this.$store.state.user.uir_student).map(item => item.titre);
        },
        store_years(){
            return this.$store.state.years;
        },
        store_user() {
            return this.$store.state.user;
        },
        store_cursus() {
            return this.$store.state.cursus;
        },
        store_profil_cin() {
            return this.$store.state.profil.cin;
        },
        preloading(){
            return this.$store.state.preloading;
        },
    },

    mounted: function() {
            this.$store.dispatch('tellGetLocations');
    },

    methods: {
        showme: function(){
            console.log(this.store_nationalites);
        },
        prevstep: function() {
            this.$router.push({ name: 'step1' });
        },
        nextstep: function(scope) {
            this.$validator.validateAll(scope).then((result) => {
                if (result) {
                    this.$store.dispatch('tellStepDone',1);
                    this.$store.dispatch('tellPreloading',true);
                    this.$store.dispatch('tellSaveCursus', {
                        niveau: this.store_cursus.niveau,
                        annee_diplome: this.store_cursus.annee_diplome,
                        country_etudes: this.store_cursus.country_etudes,
                        city_etudes: this.store_cursus.city_etudes,
                        systeme_etudes: this.store_cursus.systeme_etudes,
                        type_institut: this.store_cursus.type_institut,
                        etablissment: this.store_cursus.etablissment,
                        specialite: this.store_cursus.specialite
                    });
                    this.$toasted.global.saved();
                    this.$router.push({ name: 'step3' });
                }
            });
        },


    }

}

</script>
