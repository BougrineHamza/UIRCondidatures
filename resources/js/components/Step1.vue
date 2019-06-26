<template>
<div>
    <div class="d-flex justify-content-center align-items-center pt-5" v-if="preloading">

        <svg class="spinner mt-5" width="40px" height="40px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
            <circle class="path_green" fill="none" stroke-width="8" stroke-linecap="round" cx="33" cy="33" r="23"></circle>

        </svg>
    </div>

    <div class="mt-5" data-vv-scope="form-2" v-else>
        <div class="text-left">
            <h1 class="bonjour_title">Bonjour {{ store_user.firstname }},</h1>
            <h4 class="sous_bonjour">Parlez-nous de vous...</h4>
        </div>
        <div class="d-flex justify-content-center">
        <div style="width:320px;" class="text-center mt-4">
            <div class="mb-2">
                <div class="text-left form position-relative">
                    <small v-show="errors.has('sexe')" class="help">{{ errors.first('sexe') }}</small>

                    <div class="custom-control custom-radio custom-control-inline ">
                        <input type="radio" value="1" v-validate="'required|included:1,0'" v-model="store_profil.sexe" data-vv-as="Sexe" id="customRadioInline1" name="sexe" class="custom-control-input" :checked="store_profil.sexe == 1 ? 'checked' : ''" :disabled="dis_gender">
                        <label class="custom-control-label d-flex align-items-center" for="customRadioInline1">Homme</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" value="0" v-model="store_profil.sexe" id="customRadioInline2" name="sexe" class="custom-control-input" :checked="store_profil.sexe == 0 ? 'checked' : ''" :disabled="dis_gender">
                        <label class="custom-control-label d-flex align-items-center" for="customRadioInline2">Femme</label>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row form">
                    <div class="col text-left"><label class="col-form-label" for="cin">Votre CIN/Passeport</label></div>
                </div>
                <div class="position-relative">
                    <input v-model="store_profil.cin" name="cin" type="text" class="form-control" required="required" v-validate="'required|min:2|max:20|alpha_num'" data-vv-validate-on="blur" @blur="check_cin(store_profil.cin)" :class="{'haserror': errors.has('cin') }" data-vv-as="CIN" :disabled="dis_cin">
                    <!-- <i class="material-icons gsm_put text-success" v-if="cin_correct == true">done_all</i> -->
                    <i class="material-icons gsm_put text-danger" v-if="cin_correct == false">clear</i>

                    <small v-show="errors.has('cin')" class="help">{{ errors.first('cin') }}</small>
                </div>
            </div>
            <div class="mb-2">
                <div class="row form">
                    <div class="col text-left position-relative">
                        <label class="col-form-label" for="pays">Pays</label>
                    </div>
                    <div class="col text-left position-relative">
                        <label class="col-form-label" for="ville">Ville</label>
                    </div>
                </div>
                <div class="position-relative ">
                    <div class="input-separator d-flex">
                        <cool-select :ItemsLimit="mylimit" class="lefty" v-model="store_profil.country" :items="store_countries" name="pays" v-validate="'required'" :class="{'haserror': errors.has('pays') }" data-vv-as="Pays" :disabled="dis_country"><template slot="no-data">Aucune donnée...</template></cool-select>

                        <cool-select :ItemsLimit="mylimit"  class="righty" v-model="store_profil.city" :items="store_cities" name="ville" v-validate="'required'" :class="{'haserror': errors.has('ville') }" data-vv-as="Ville" :disabled="dis_city"><template slot="no-data">Aucune donnée...</template></cool-select>
                    </div>
                    <small v-show="errors.has('pays') || errors.has('ville')" class="help">
                        {{ errors.first('pays') }} <br v-if="errors.has('pays') && errors.has('ville')" />
                        {{ errors.first('ville') }}
                    </small>
                </div>
            </div>
            <div class="mb-2">
                <div class="row form">
                    <div class="col text-left"><label class="col-form-label" for="address">Votre Adresse</label></div>
                </div>
                <div class="position-relative">
                    <input v-model="store_profil.address" name="address" type="text" class="form-control" v-validate="'required'" :class="{'haserror': errors.has('address') }" data-vv-as="Adresse" :disabled="dis_address">
                    <small v-show="errors.has('address')" class="help">{{ errors.first('address') }}</small>
                </div>
            </div>
            <div class="mb-3">
                <div class="row form">
                    <div class="col text-left">
                        <label class="col-form-label" for="address">Votre numéro GSM</label>
                    </div>
                </div>
                <div class="position-relative">
                <input-phone @keypress.enter="nextstep()" large="" :mynumber="store_profilgsm" @phonefc="phone_fp"></input-phone>

                    <!-- <input  v-model="store_profil.gsm" name="gsm" type="text" class="form-control" v-validate="'required|numeric|max:30'" placeholder="Ex:0661..." :class="{'haserror': errors.has('gsm') }" data-vv-as="Numéro GSM"> -->

                    <i class="material-icons gsm_put" style="z-index: 3;">phonelink_ring</i>
                    <small v-show="phonex == true" class="help">Le champs Numéro GSM est requis</small>


                    <!-- <small v-show="errors.has('gsm')" class="help">{{ errors.first('gsm') }}</small> -->
                </div>
            </div>
            <div class="form text-left mb-2">
                <div class="custom-control custom-checkbox d-flex align-items-center position-relative">
                    <input v-model="store_profil.cndp" type="checkbox" class="custom-control-input" id="customCheck1" name="cndp" v-validate="'required'" :class="{'haserror': errors.has('cndp') }" data-vv-as="CGU">
                    <small v-show="errors.has('cndp')" class="help">{{ errors.first('cndp') }}</small>
                    <label class="custom-control-label cndp d-flex align-items-end font-weight-light text-muted" for="customCheck1">J’accèpte les termes des CGU du site UIR</label>
                </div>
            </div>
            {{store_disabled}}


            <div class="text-right">
                <button class="btn btn-primary" type="button" @click="nextstep()">Suivant</button>
            </div>
        </div></div>
    </div>


    <div class="modal fade" tabindex="-1" role="dialog" id="impossible">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <h5 class="modal-title text-uppercase d-flex align-items-center"><i class="material-icons mr-2">notifications_none</i> Attention !</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="font-weight-light">Un candidat a déjà inscrit ce numéro de CIN.</p>

                        <div class="spam d-flex align-items-center">
                            <span class="ml-2">PS: Veuillez contacter notre <a href="#" @click.prevent="gotosupport()">équipe de support</a>.</span>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    </div>
</template>
<script>
import { CoolSelect } from 'vue-cool-select';
import axios from 'axios';
import VueAxios from 'vue-axios';

export default {
    components: { CoolSelect },

    data() {
        return {
            dis_gender: false,
            dis_cin:false,
            dis_country: false,
            dis_city: false,
            dis_address: false,
            phonex: false,
            cin_correct: null,
            mylimit: 100
        }
    },


    created: function() {

        this.$store.dispatch('tellGetLocations');

    },

    mounted: function() {
        this.phonex = this.store_profilgsm;
    },

    computed: {
        preloading(){
            return this.$store.state.preloading;
        },
        store_user() {
            return this.$store.state.user;
        },
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
        store_disabled(){
            this.dis_gender = this.$store.state.disabled.gender;
            this.dis_cin = this.$store.state.disabled.cin;
            this.dis_country = this.$store.state.disabled.country;
            this.dis_city = this.$store.state.disabled.city;
            this.dis_address = this.$store.state.disabled.address;

            return console.log('Champs désactivés');
        },
        store_profil(){
            return this.$store.state.profil;
        },
        store_profilgsm(){
            return this.$store.state.profil.gsm;
        },
        store_profilcin(){
            return this.$store.state.profil.cin;
        },
        store_cities() {
            if (this.store_profil.country) {
                return this.$store.getters['getCities'](this.store_profil.country).cities.map(item => item.titre).sort(function (a, b) {
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
        }
    },

    watch: {
        store_profilgsm(newVal,oldVal){
            this.phonex = newVal;

        }
    },

    methods: {
        gotosupport: function(){
            this.$router.push({ name: 'support' });

            $('#impossible').modal('hide');

        },
        check_cin: function(val) {
            axios.get('/api/candidat/check_cin?api_token='+localStorage.getItem("api_token_uir")+'&cin='+this.store_profil.cin)
            .then(response => {
                if(response.data.statut == true){

                    this.cin_correct = true;

                } else {

                    this.store_profil.cin = null;

                    this.cin_correct = false;

                    $('#impossible').modal('show');

                }
            }).catch(error => {

            });
        },
        phone_fp: function(val) {
            this.phonex = val;
        },
        nextstep: function(scope) {
            if(this.phonex != false && this.phonex != true && this.phonex != null){
            this.$validator.validateAll(scope).then((result) => {
                if (result) {
                    this.$store.dispatch('tellStepDone',0);
                    this.$store.dispatch('tellSaveProfile',{
                        sexe: this.store_profil.sexe,
                        cin: this.store_profil.cin,
                        country: this.store_profil.country,
                        city: this.store_profil.city,
                        address: this.store_profil.address,
                        gsm: this.phonex,
                        cndp: this.store_profil.cndp
                    });
                    this.$toasted.global.saved();
                    this.$router.push({ name: 'step2' });
                }
            });
        } else {
            this.phonex = true;
        }
    },


    }

}

</script>
