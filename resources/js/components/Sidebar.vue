<template>
    <div v-sticky="{ zIndex: 2, stickyTop: 20, className: 'sticky-buttons' }">
        <div class="row mt-4 no-gutters">
            <div class="col d-flex justify-content-center mt-5">
                <div class="care-uir">
                    <div class="icon-slider row no-gutters" :class="['mylevel'+step_level, 'care-grad'+step_level]">
                        <div class="icon-etape d-flex align-items-center justify-content-center"><img src="images/ico0.svg"></div>
                        <div class="icon-etape position-relative d-flex align-items-center justify-content-center"><img src="images/ico1.svg" style="position:absolute;bottom:-2px;right:0;"></div>
                        <div class="icon-etape d-flex align-items-center justify-content-center"><img src="images/ico2.svg"></div>
                        <div class="icon-etape d-flex align-items-center justify-content-center"><img src="images/ico3.svg"></div>
                        <div class="icon-etape d-flex align-items-center justify-content-center"><img src="images/ico4.svg"></div>
                        <div class="icon-etape d-flex align-items-center justify-content-center"><img src="images/ico5.svg"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row no-gutters">
            <div :class="step_level == 5 ? 'col-md-6 text-center offset-md-3' : 'col'">
                <h1 id="gtitre" class="text-uppercase grand-titre mt-5" v-if="step_level == 0">Déposez vos candidatures</h1>
                <h1 id="gtitre" class="text-uppercase grand-titre mt-5" v-else-if="step_level == 1">Renseignez votre Profil</h1>
                <h1 id="gtitre" class="text-uppercase grand-titre mt-5" v-else-if="step_level == 2">Quel est votre cursus?</h1>
                <h1 id="gtitre" class="text-uppercase grand-titre mt-5" v-else-if="step_level == 3">Choisissez votre formation UIR</h1>
                <h1 id="gtitre" class="text-uppercase grand-titre mt-5" v-else-if="step_level == 4">Réservez votre RDV Entretien</h1>
                <h1 id="gtitre" class="text-uppercase grand-titre mt-5" v-else-if="step_level == 5">Convocation UIR envoyée par e-mail</h1>
                <h1 id="gtitre" class="text-uppercase grand-titre mt-5" v-else-if="step_level == 6">Téléchargez votre convocation</h1>
                <h1 id="gtitre" class="text-uppercase grand-titre mt-5" v-else-if="step_level == 7">Modifier vos paramètres?</h1>
                <h1 id="gtitre" class="text-uppercase grand-titre mt-5" v-else>Merci de nous faire confiance</h1>
                <div v-if="step_level == 0">
                    <p id="stitre" class="sous-titre pt-2">Identifiez-vous et déposez vos candidatures en toute facilité avec UIR</p>
                </div>
                <div v-else-if="step_level == 1">
                    <p id="stitre" class="sous-titre pt-2">Vos informations seront reprises sur votre convocation</p>
                </div>
                <div v-else-if="step_level == 2">
                    <p id="stitre" class="sous-titre pt-2">Nous vous proposerons des formations en adéquation avec votre cursus</p>
                </div>
                <div v-else-if="step_level == 3">
                    <p id="stitre" class="sous-titre pt-2">Vous pouvez choisir plusieurs formations</p>
                </div>
                <div v-else-if="step_level == 4">
                    <p id="stitre" class="sous-titre pt-2">Choisissez les dates/horaires d'entretiens qui vous conviennent</p>
                </div>
                <div v-else-if="step_level == 6">
                    <p id="stitre" class="sous-titre pt-2">Présentez-vous à vos rendez-vous d'entretiens munis de votre dernière convocation valide</p>
                </div>
                <div v-else-if="step_level == 7">
                    <p id="stitre" class="sous-titre pt-2">Prenez le contrôle de vos paramètres de compte pour gérer votre e-mail et votre mot de passe</p>
                </div>
                <div v-else-if="step_level == 5">
                    <p id="stitre" class="sous-titre pt-2">Veuillez consulter votre boite e-mail pour télécharger votre convocation:<br />
                        <span class="text-green">{{myemail}}</span></p>
                    <button class="btn btn-primary" @click="goto_link('mes-convocations')">Télécharger convocation</button>
                    <div class="spam d-flex align-items-center mt-5">
                        <svg version="1.1" id="Forma_1_1_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="23.167px" height="21.333px" viewBox="0 0 23.167 21.333" enable-background="new 0 0 23.167 21.333" xml:space="preserve">
                            <g id="Forma_1">
                                <path fill-rule="evenodd" clip-rule="evenodd" fill="#CDA540" d="M11.96,15.71c-0.273-0.273-0.683-0.273-0.956,0
        c-0.137,0.137-0.137,0.41-0.137,0.546c0,0.273,0,0.41,0.137,0.546c0.137,0.137,0.409,0.137,0.546,0.137s0.409,0,0.273-0.137
        c0.136-0.137,0.273-0.41,0.273-0.546C12.097,15.983,12.097,15.846,11.96,15.71z M11.551,7.104c-0.409,0-0.683,0.273-0.683,0.683
        v4.918c0,0.41,0.273,0.683,0.683,0.683c0.409,0,0.683-0.273,0.683-0.683V7.787C12.233,7.377,11.96,7.104,11.551,7.104z
         M22.608,15.846l-8.054-14.07C14.008,0.683,12.779,0,11.551,0C10.322,0,9.23,0.684,8.548,1.776l-8.054,14.07
        C0.217,16.289,0.052,16.777,0,17.273v0.697c0.052,0.496,0.217,0.984,0.494,1.427c0.546,1.093,1.775,1.776,3.003,1.776h16.108
        c1.365,0,2.457-0.683,3.003-1.776C23.291,18.305,23.291,16.939,22.608,15.846z M21.516,18.852
        c-0.273,0.683-0.956,1.093-1.775,1.093H3.497c-0.683,0-1.365-0.41-1.775-1.093c-0.41-0.683-0.41-1.503,0-2.186l8.054-14.07
        c0.273-0.683,0.956-1.093,1.775-1.093c0.819,0,1.502,0.41,1.911,1.093l8.054,14.07C21.926,17.349,21.926,18.168,21.516,18.852z" />
                            </g>
                        </svg>
                        <span class="ml-2">Merci de consulter votre SPAM également.</span></div>
                </div>
                <div v-else>
                    <p id="stitre" class="sous-titre pt-2">Nous sommes très heureux de vous compter parmi nos candidats</p>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import VueSticky from 'vue-sticky';
export default {
    props: [],

    data() {
        return {
            myemail: '',

        }
    },

    mounted: function() {},
    directives: {
  'sticky': VueSticky,
},

    methods: {
        animate_titre: function() {
            var element1 = document.getElementById("gtitre");
            var element2 = document.getElementById("stitre");
            element1.classList.remove("animate_titre1");
            element2.classList.remove("animate_titre2");
            void element1.offsetWidth;
            void element2.offsetWidth;
            element1.classList.add("animate_titre1");
            element2.classList.add("animate_titre2");
        },
        noflex: function() {
            document.getElementById('separator').classList.remove('noflex');
            document.getElementById('mymenu').classList.remove('menu-fixed');
            document.getElementById('rightpanel').classList.add('d-none');
            document.getElementById('rightpanel').classList.remove('pl-2');
            document.getElementById('rightpanel').classList.add('px-5');
        },
        goto_link: function(where) {

            if(where == 'mes-convocations'){
                this.$store.dispatch('tellGetMyConvocations');
            }

            this.$store.dispatch('tellPreloading',true);
            this.$router.push({ name: where });
        }
    },
    computed: {
        step_level() {
            if (this.$store.state.step_level == 5) {
                this.myemail = this.$store.state.user.email;
                document.getElementById('rightpanel').classList.remove('d-none');
                document.getElementById('rightpanel').classList.remove('px-5');
                document.getElementById('rightpanel').classList.add('pl-2');
                document.getElementById('separator').classList.add('noflex');
                document.getElementById('mymenu').classList.add('menu-fixed');
            } else {
                setTimeout(this.noflex, 0);
            }

            setTimeout(this.animate_titre, 0);

            return this.$store.state.step_level;
        }
    }

}

</script>
