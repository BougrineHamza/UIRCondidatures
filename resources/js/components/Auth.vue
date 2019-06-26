<template>
    <div class="mt-5 text-center" style="width:320px;" data-vv-scope="form-1">
        <h4 class="grand-titre2 text-uppercase text-center">Authentification</h4>
        <span>{{tag}}</span>
        <div class="my-5">
            <label class="switch" for="checkbox">
                <input type="checkbox" v-model="inscription" id="checkbox" @change="action_inscription(inscription)" />
                <div class="slider"></div>
                <span class="text-uppercase label label-left">Connexion</span>
                <span class="text-uppercase label label-right">Inscription</span>
            </label>
        </div>
        <div>
            <div class="row mb-4">
                <div class="col d-flex justify-content-between align-items-center text-uppercase tabs-identification">
                    <div data-toggle="tooltip" title="Je ne suis pas encore un étudiant UIR">
                    <span :class="{ active: !uir_student }"  @click="uir_student_tab(false),tellAuthType(1)"><!-- <font v-if="inscription && !inscription_forcee">Je suis </font> -->Externe</span>
                    </div>
                    <div data-toggle="tooltip" title="Je suis un étudiant UIR">
                    <span :class="{ active: uir_student }" v-if="connexion || inscription_forcee" @click="uir_student_tab(true),tellAuthType(2)" >Etudiant UIR</span>
                    </div>
                </div>
            </div>
            <div>
                <div class="mb-2" v-if="!connexion">
                    <div class="row form">
                        <div class="col text-left position-relative">
                            <label class="col-form-label" for="prenom">Prénom</label>
                        </div>
                        <div class="col text-left position-relative">
                            <label class="col-form-label" for="nom">Nom</label>
                        </div>
                    </div>
                    <div class="position-relative ">
                        <div class="input-group input-separator">
                            <input v-model="firstname" type="text" name="prenom" class="form-control text-capitalize" required="required" v-validate="'required|max:20|min:3'" :class="{'haserror': errors.has('prenom') }" data-vv-as="Prénom" />
                            <input v-model="lastname" type="text" name="nom" class=" text-uppercase form-control" required="required" v-validate="'required|max:20|min:3'" :class="{'haserror': errors.has('nom') }" data-vv-as="Nom" />
                        </div>
                        <small v-show="errors.has('nom') || errors.has('prenom')" class="help">
                            {{ errors.first('prenom') }} <br v-if="errors.has('nom') && errors.has('prenom')" />
                            {{ errors.first('nom') }}
                        </small>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row form">
                        <div class="col text-left"><label class="col-form-label" for="email">Votre e-mail</label></div>
                    </div>
                    <div class="position-relative">
                        <input :placeholder="uir_student ? '' : 'Ex: prenom@gmail.com'" @input="mask()" v-model="email" name="email" type="email" class="form-control" required="required" v-validate="'required|email'" :class="{'haserror': errors.has('email') }" data-vv-as="E-mail">
                        <small v-show="errors.has('email')" class="help">{{ errors.first('email') }}</small>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="row form">
                        <div class="col text-left"><label class="col-form-label" for="password">Mot de passe</label></div>
                    </div>
                    <div class="password position-relative">
                        <input @keypress.enter="nextstep(auth_type)" v-model="password" :type="show_password" name="password" class="form-control" v-validate="'required'" :class="{'haserror': errors.has('password') }" data-vv-as="Mot de passe">
                        <label class="show-pass" for="passw">
                            <input type="checkbox" id="passw" name="passw" v-model="show_pass" @change="showmypassword(show_pass)" />
                            <i class="material-icons"></i>
                        </label>
                        <small v-show="errors.has('password')" class="help">{{ errors.first('password') }}</small>
                    </div>
                </div>
                <div>
                    <div class="text-right d-flex justify-content-between align-items-center">
                        <i v-if="!connexion"></i>
                        <a href="#" @click.prevent="mdp()" class="mdp_oublie" v-if="connexion">Mot de passe oublié ?</a>
                        <button :id="inscription ? 'inscription':'connexion'" class="btn" :class="!spinner ? 'btn-primary' : 'btn-secondary'"  type="button" @click="!spinner ? nextstep(auth_type) : ''"><svg v-if="spinner" class="spinner" width="10px" height="10px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                                <circle class="path" fill="none" stroke-width="22" stroke-linecap="round" cx="33" cy="33" r="23"></circle>
                            </svg><span v-else>Suivant</span></button></div>
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
            tag:'Inscrivez-vous pour un entretien...',
            connexion: false,
            inscription: true,
            uir_student: false,
            show_pass: false,
            show_password: 'password',
            firstname: '',
            lastname: '',
            email: '',
            password: '',
            auth_type: 0,
            inscription_forcee: false,
            connexion_forcee: false

        }
    },

    computed: {
        spinner() {
            return this.$store.state.pending;
        },

        store_inscription_forcee(){
            return this.$store.state.inscription_forcee;
        },

        store_connexion_forcee(){
            return this.$store.state.connexion_forcee;
        },

    },

    watch: { // On écoute les valeurs changées automatiquement dans le Store
        // Si utilisateur UIR valide O365 mais non Inscrit dans notre liste
        store_inscription_forcee(newVal, oldVal){
            if(newVal == true){
                this.inscription_forcee = true;
                this.inscription = true;
                this.uir_student = true; // On coche Etudiant UIR -> NON
                this.connexion = false; // On ouvre le volet inscription
                this.tellAuthType(0); // On met à jour le type d'authentification sur Inscription
            }
        },// Si utilisateur UIR valide O365 mais non Inscrit dans notre liste
        store_connexion_forcee(newVal, oldVal){
            if(newVal == true){
                this.connexion_forcee = true;
                this.inscription = false;
                this.uir_student = false; // On coche Etudiant UIR -> NON
                this.connexion = true; // On ouvre le volet inscription
                this.tellAuthType(2); // On met à jour le type d'authentification sur Inscription
            }
        }
    },

    mounted: function() {
        // Si URL d'authentification directement
        if(this.$router.history.current.name == 'connexion'){
            this.$store.dispatch('tellFrocerConnexion',true);
        }

        // Si l'utilisateur pointe sur cette URL alors qu'il est connecté on le renvoi
        if (this.$store.state.connected == true) {
            // this.$store.dispatch('tellStepLevel', 3);
            this.$router.push({ name: "step1" });
        }
    },

    methods: {
        tellAuthType(type) {
            this.auth_type = type;
        },
        mdp: function() {
            this.$router.push({ name: 'mdp-oublie' });
        },
        nextstep: function(type) {
            this.$validator.validateAll().then((result) => {
                if (result) {
                    this.$store.dispatch('tellPreloading',true);

                    if(type == 1){ // Si Connexion mais pas via Utilisateur UIR

                        if(this.email.includes("@uir.ac.ma")){
                            this.$store.dispatch("tellUIRStudent", {
                            email: this.email,
                            password: this.password,
                            router: this.$router,
                            toaster: this.$toasted,
                            });
                        } else {

                            this.$store.dispatch('tellUser', {
                                firstname: this.firstname,
                                lastname: this.lastname,
                                email: this.email,
                                password: this.password,
                                uir_student: this.uir_student
                            });


                            this.$store.dispatch("tellConnect", {
                                auth_type: type,
                                email: this.email,
                                password: this.password,
                                router: this.$router,
                                toast: this.$toasted
                            });

                        }

                    } else if(type == 0) { // Si Inscription

                        this.$store.dispatch("tellInscription", {
                            first_name: this.firstname,
                            last_name: this.lastname,
                            email: this.email,
                            password: this.password,
                            router: this.$router,
                            toaster: this.$toasted
                        });


                    } else if(type==2){  // Si Connexion et via Utilisateur UIR
                        this.$store.dispatch("tellUIRStudent", {
                            email: this.email,
                            password: this.password,
                            router: this.$router,
                            toaster: this.$toasted,

                        });
                    }


                }
            });
        },
        mask: function() {
            if (this.uir_student) {
                var oldstr = this.email;
                var str = oldstr.replace('@uir.ac.ma', '');
                this.email = str + '@uir.ac.ma';
            }
        },
        uir_student_tab: function(bool) {
            this.uir_student = bool;

            if (this.uir_student) {
                this.email = "@uir.ac.ma";
                this.mask();
            } else {
                this.email = null;
            }
        },
        action_inscription(bool){
            this.connexion = !bool;

            if(bool == false){
                this.tag = 'Connectez-vous à votre espace...';
                this.uir_student_tab(false);

                if(this.uir_student == true){
                    this.auth_type = 2;
                } else {
                    this.auth_type = 1;
                }

            } else {
                this.tag = 'Inscrivez-vous pour un entretien...';
                this.uir_student_tab(false);
                this.auth_type = 0;


            }
        },
        showmypassword: function(bool) {
            if (bool) {
                this.show_password = 'text';
            } else {
                this.show_password = 'password';
            }
        }

    }

}

</script>
