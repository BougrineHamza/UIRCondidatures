<template><div class="mt-5 w-100">
    <div class="d-flex justify-content-center align-items-center pt-5" v-if="preloading">

        <svg class="spinner mt-5" width="40px" height="40px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
            <circle class="path_green" fill="none" stroke-width="8" stroke-linecap="round" cx="33" cy="33" r="23"></circle>

        </svg>
    </div>
<div class="w-100 row no-gutters" v-else>
    <div v-if="!store_mesformations" class="col-md-10 offset-md-1 text-center ">
        <div class="text-center img-fluid w-100">
            <h4 class="sous_bonjour text-center mb-5 text-danger">Pas de rendez-vous disponible pour vos formations choisies</h4>
            <svg version="1.1" id="Calque_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="214px" height="214px" viewBox="0 0 214 214" enable-background="new 0 0 214 214" xml:space="preserve">
                <path fill="#DBE1EC" d="M65.419,111.13l18.394,18.4c1.613,1.606,1.613,4.219,0,5.831c-0.806,0.806-1.869,1.206-2.919,1.206
	c-1.05,0-2.106-0.4-2.913-1.213l-25.438-25.444c-1.612-1.606-1.612-4.219,0-5.831l25.438-25.438c1.606-1.613,4.219-1.613,5.831,0
	c1.613,1.606,1.613,4.219,0,5.831l-18.394,18.4h148.038C211.287,45.969,164.479,0.5,107.044,0.5C48.225,0.5,0.544,48.182,0.544,107
	c0,58.818,47.682,106.5,106.5,106.5c57.435,0,104.242-45.467,106.413-102.37H65.419z" />
            </svg>
            <p class="mt-5 text-center font-weight-light">Veuillez choisir une autre formation car celles-ci ne dispensent d'aucun RDV disponible.
            </p>
            <button @click="prevstep()" class="btn btn-secondary text-uppercase">Précedent</button>
        </div>
    </div>
    <div data-vv-scope="form-5" class="w-100" v-else>
        <div class="text-center col-md-10 offset-md-1">
            <h4 class="sous_bonjour text-center mb-4">Quand voulez-vous passer vos entretiens ?</h4>
            <div class="row mb-4" v-for="(forma,index) in store_mesformations">
                <div class="col">
                    <div class="row d-flex align-items-center mb-2 colps" data-toggle="collapse" :data-target="'#liste'+index" aria-expanded="false" :aria-controls="'liste'+index" >
                        <div class="col-md-3 text-left">
                            <img :src="'/images/'+forma.miniature" width="100" class="mb-md-0 mb-2" />
                        </div>
                        <div class="col-md-9 pl-md-0 d-flex justify-content-between align-items-center">
                            <h6 class="formation_choisie text-left m-0">{{ forma.titre }}</h6> <i class="material-icons text-muted d-md-inline d-none">keyboard_arrow_down</i>
                        </div>
                    </div>
                    <div v-if="store_dispordv[index] != undefined" class="collapse show" :id="'liste'+index">
                    <div class="row mb-2" v-for="(date,indexus) in store_dispordv[index].disponibilites" v-if="date.date_concours > today">
                        <div class="col-md-3">
                            <div class="date_container d-flex align-items-center justify-content-center">
                                <div v-if="date.uirformation_id == forma.id">
                                    <span class="d-block jour">{{date.date_concours.slice(8,10)}}</span>
                                    <span v-if="date.date_concours.slice(5,7) == 1">Janvier</span>
                                    <span v-else-if="date.date_concours.slice(5,7) == 2">Février</span>
                                    <span v-else-if="date.date_concours.slice(5,7) == 3">Mars</span>
                                    <span v-else-if="date.date_concours.slice(5,7) == 4">Avril</span>
                                    <span v-else-if="date.date_concours.slice(5,7) == 5">Mai</span>
                                    <span v-else-if="date.date_concours.slice(5,7) == 6">Juin</span>
                                    <span v-else-if="date.date_concours.slice(5,7) == 7">Juillet</span>
                                    <span v-else-if="date.date_concours.slice(5,7) == 8">Août</span>
                                    <span v-else-if="date.date_concours.slice(5,7) == 9">Septembre</span>
                                    <span v-else-if="date.date_concours.slice(5,7) == 10">Octobre</span>
                                    <span v-else-if="date.date_concours.slice(5,7) == 11">Novembre</span>
                                    <span v-else-if="date.date_concours.slice(5,7) == 12">Décembre</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9 pl-md-0">
                            <div class="rdv_container p-3 text-left" :style="'border-color:'+forma.color">
                                <span data-toggle="tooltip" title="Confirmer/Annuler réservation de cet horaire d'entretien"

                                 v-for="(rdv,indexas) in date.concourtime"

                                 class="rdv_pill mr-1 mt-1"

                                 :class="{'ismine' : myrdv.indexOf(rdv.id) >= 0}"

                                 v-on="myrdv.indexOf(rdv.id) >= 0 ? {click: () => remove_rdv(rdv.id,rdv.time,date) } : {click: () => add_rdv(rdv.id,rdv.time,date) }">{{rdv.time}}
                                </span>

                                <div v-if="date.concourtime.length == 0" class="d-flex  justify-content-center pt-2"><br />Tous les horaires sont réservés
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div :id="'liste'+index" class="collapse show" v-else>
                        <div class="rdv_container d-flex align-items-center justify-content-center" :style="'border-color:'+forma.color">
                            Programmation à venir...
                        </div>
                        <div class="modal fade" tabindex="-1" role="dialog" :id="'sur_dossier'+forma.id">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-uppercase"><img :src="'/images/'+forma.miniature" width="100" class="mr-2" /> Dossier de candidature</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-08 text-left">
                                        <h6>
                                        <b :style="'color:'+forma.color">{{forma.titre}}</b></h6>
                                        <hr />
                                        <div v-html="forma.sur_dossier"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div @click="prevstep()" class="goback d-flex align-items-center"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="35.645px" height="8.175px" viewBox="0 0 35.645 8.175" enable-background="new 0 0 35.645 8.175" xml:space="preserve">
                        <g>
                            <path d="M34.83,3.455H1.913l2.499-2.499c0.219-0.219,0.219-0.574,0-0.792c-0.219-0.219-0.574-0.219-0.792,0L0.164,3.619
        c-0.219,0.219-0.219,0.574,0,0.792l3.455,3.455c0.109,0.109,0.253,0.164,0.396,0.164c0.143,0,0.287-0.055,0.396-0.164
        c0.219-0.219,0.219-0.574,0-0.792L1.913,4.576H34.83c0.309,0,0.56-0.251,0.56-0.56C35.39,3.706,35.139,3.455,34.83,3.455z" />
                        </g>
                    </svg>
                </div>
                <button class="btn" :class="myrdv.length > 0 ? 'btn-primary' : 'btn-secondary'" type="button" @click="open_modal(myrdv.length)">Suivant</button>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="confirmer">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-uppercase">CONFIRMEZ-VOUS VOS RENDEZ-VOUS ?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="lead text-center">Merci de confirmer vos rendez-vous pour vous envoyer votre convocation officielle.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button v-if="myrdv.length > 0" type="button" class="btn btn-primary" @click="nextstep()" data-dismiss="modal">Confirmer</button>
                    </div>
                </div>
            </div>
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
                        <p class="font-weight-light">Vous avez déjà réservé un entretien pour cette formation.</p>

                        <div class="spam d-flex align-items-center">
                            <span class="ml-2">PS: Vous avez droit à un seul entretien par formation.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="impossible2">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <h5 class="modal-title text-uppercase d-flex align-items-center"><i class="material-icons mr-2">notifications_none</i> Attention !</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="font-weight-light">Vous avez déjà réservé un entretien à cette date et heure.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div></div>
</template>
<script>
export default {
    props: [],

    data() {
        return {
            myrdv: [],
            myforma: [],
            mydates: [],
            today: ''

        }
    },

    mounted: function() {
        $('[data-toggle="tooltip"]').tooltip({ trigger: "hover" });

        var todays = new Date();
        var dd = String(todays.getDate()).padStart(2, '0');
        var mm = String(todays.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = todays.getFullYear();

       this.today = yyyy + '-' + mm + '-' + dd;

    },
    computed: {
        preloading(){
            return this.$store.state.preloading;
        },
        store_mesformations: function() {
        		return this.$store.getters.getMyFormations;
        },
        store_dispordv: function() {
        	this.myrdv = this.$store.state.myrdv_time_id;
            this.myforma = this.$store.state.myforma_time_id;
            this.mydates = this.$store.state.mydates_time_id;

            return this.$store.getters['getDisposRDV'](this.$store.state.formations_choisies);
        },
    },

    methods: {
        open_modal: function(value){
            if(value > 0){
            $('#confirmer').modal('show');
        } else {
            this.$toasted.show('Veuillez choisir un RDV...', {
                        icon : 'do_not_disturb',
                        duration: 2500,
                        closeOnSwipe: true
                    });
        }
        },
        remove_rdv: function(rdv_id,time,date) {
            // On recense l'ID de la formation dont le RDV est choisi
            var forma_id = this.$store.state.liste_rdv_dispo.find(el => el.disponibilites.some(elem => elem.concourtime.some(element => element.id == rdv_id))).formation_id;

            // On recense la date et créneau du concours
            var date_id = date.date_concours + '-' + time;

            // On retire l'ID de la formation dont le RDV choisi de notre Array
            this.myforma.splice( this.myforma.indexOf(forma_id), 1 );

            // On retire l'ID du RDV de notre Array
            this.myrdv.splice( this.myrdv.indexOf(rdv_id), 1 );


            // On retire le créneau RDV de notre Array
            this.mydates.splice( this.mydates.indexOf(date_id), 1 );


            return this.$store.dispatch('tellSaveMyRDV', this.myrdv);
        },
        add_rdv: function(rdv_id,time,date) {
            // On recense l'ID de la formation dont le RDV est choisi
            var forma_id = this.$store.state.liste_rdv_dispo.find(el => el.disponibilites.some(elem => elem.concourtime.some(element => element.id == rdv_id))).formation_id;

            // On recense la date et créneau du concours
            var date_id = date.date_concours + '-' + time;


            // Si on a déjà un RDV pour cette formation
            if(this.myforma.indexOf(forma_id) == -1){


                if(this.mydates.indexOf(date_id) == -1){
                    // On ajoute ce créneau dans notre Array
                    this.mydates.push(date_id);

                    // On ajoute l'ID du RDV choisi dans notre Array
                    this.myrdv.push(rdv_id);

                    // On enregistre dans l'Array les Formations dont les RDV sont choisis
                    this.myforma.push(forma_id);

                    return this.$store.dispatch('tellSaveMyRDV', this.myrdv);

                } else {
                $('#impossible2').modal('show');
                }



            } else {
                $('#impossible').modal('show');
            }

        },
        prevstep: function() {
            this.$router.push({ name: 'step3' });
        },
        nextstep: function() {
            this.$store.dispatch('tellStepLevel', 5);
            this.$store.dispatch('tellDisable');
            this.$store.dispatch('tellEmailConvocationCreee');
            this.$store.dispatch('tellStepDone',3);
            this.$toasted.show('Convocation créée...', {
                        icon : 'check',
                        duration: 2500,
                        className: 'mytoast',
                        closeOnSwipe: true
                    });
            this.$store.dispatch('tellSaveMyRDV_DB', this.myrdv);
            // this.$store.dispatch('tellNewConvocation');
            this.$router.push({ name: 'done' });
        }


    }

}

</script>
