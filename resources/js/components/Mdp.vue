<template>
    <div>
    <div class="mt-5 text-center"  data-vv-scope="form-1">
        <h4 class="grand-titre2 text-uppercase text-center">Mot de passe oublié?</h4>
        <span class="pb-5">Recevez un nouveau mot de passe par e-mail</span>
        <div class="mt-5" style="width:320px; margin:auto;">
        <!-- <div class="mt-5 mb-4">
            <label class="switch" for="checkbox">
                <input type="checkbox" v-model="guest" id="checkbox" @change="is_student(guest)" />
                <div class="slider"></div>
                <span class="text-uppercase label label-left">Oui</span>
                <span class="text-uppercase label label-right">Non</span>
            </label>
        </div> -->

        <div class="mt-3 d-flex justify-content-center">
                <div class="circle_noconvo">
                    <svg version="1.1" id="Calque_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
     width="248.581px" height="175.106px" viewBox="0 0 248.581 175.106" enable-background="new 0 0 248.581 175.106"
     xml:space="preserve">
<g>
    <path fill="#FFFFFF" d="M183.133,100.401c-2.687,0-5.212,1.046-7.112,2.944c-1.9,1.899-2.946,4.423-2.946,7.107
        c0,2.685,1.046,5.209,2.946,7.107c1.9,1.898,4.426,2.944,7.112,2.944c2.687,0,5.212-1.046,7.112-2.944
        c1.9-1.898,2.946-4.422,2.946-7.107c0-2.685-1.046-5.209-2.946-7.107C188.345,101.446,185.819,100.401,183.133,100.401z"/>
    <path fill="#FFFFFF" d="M0,23.106v151.327h229.471V23.106H0z M193.914,121.226c-2.88,2.878-6.709,4.463-10.782,4.463
        c-3.194,0-6.236-0.979-8.79-2.788l-10.292,10.287l2.639,2.636c1.013,1.013,1.013,2.654,0,3.667
        c-0.507,0.506-1.171,0.759-1.835,0.759c-0.664,0-1.328-0.253-1.835-0.759l-2.638-2.636l-3.669,3.667l5.62,5.616
        c1.013,1.013,1.013,2.654,0,3.667c-0.507,0.506-1.171,0.759-1.835,0.759c-0.664,0-1.328-0.253-1.835-0.759l-5.62-5.616
        l-2.638,2.637c-0.506,0.507-1.171,0.76-1.835,0.76c-0.664,0-1.328-0.253-1.835-0.759c-1.013-1.012-1.013-2.654,0-3.667l4.45-4.447
        c0.008-0.008,0.014-0.017,0.022-0.025c0.008-0.008,0.017-0.014,0.025-0.022l7.299-7.294c0.005-0.005,0.01-0.012,0.015-0.017
        c0.006-0.006,0.012-0.01,0.017-0.016l12.111-12.104c-1.809-2.552-2.788-5.591-2.788-8.782c0-4.07,1.586-7.896,4.466-10.774
        c2.88-2.878,6.709-4.463,10.782-4.463c4.073,0,7.902,1.585,10.781,4.463c2.88,2.878,4.466,6.704,4.466,10.773
        C198.38,114.522,196.794,118.348,193.914,121.226z M114.756,109.664L7.495,41.412l107.745,59.054L220.961,39.96L114.756,109.664z"
        />
</g>
</svg>


                </div>
            </div>
        <div class="pt-5">
            <div><h6 class="font-weight-light text-muted text-left mb-4">Veuillez renseigner votre adresse e-mail:</h6>
                <div class="mb-2">
                    <div class="row form">
                        <div class="col text-left"><label class="col-form-label" for="email">Votre e-mail</label></div>
                    </div>
                    <div class="position-relative">
                        <input placeholder="Ex: prenom@gmail.com" @input="mask()" v-model="email"  name="email" type="email" class="form-control" required="required" v-validate="'required|email'" :class="{'haserror': errors.has('email') }" data-vv-as="E-mail">
                        <small v-show="errors.has('email')" class="help">{{ errors.first('email') }}</small>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="#" class="mdp_oublie" @click.prevent="goto('auth')">Me connecter</a>
                        <button class="btn btn-primary" type="button"  @click="sendmail()">Envoyer</button>
                    </div>
                </div>
            </div>
        </div>
    </div></div></div>
</template>
<script>
export default {
    props: [],

    data() {
        return {
            guest: true,
            connexion: false,
            email: ''

        }
    },

    computed: {

        preloading(){
            return this.$store.state.preloading;
        }
    },


    methods: {
        goto: function(where){
            this.$router.push({name: where});
        },
        mask: function(){
            if(!this.guest){
            var oldstr= this.email;
            var str=oldstr.replace('@uir.ac.ma','');
            this.email = str+'@uir.ac.ma';
            }
        },
        is_student: function(bool) {
            this.connexion = !bool;

            if(!this.guest){
                this.email = "@uir.ac.ma";
            } else { this.email = null; }
        },
        sendmail: function(scope) {
            this.$validator.validateAll(scope).then((result) => {
                if (result) {
                    this.$toasted.show('E-mail en cours d\'envoi...', {
                        icon : 'mail_outline',
                        duration: 2500,
                        closeOnSwipe: true
                    });

                    this.$store.dispatch('tellEmailMDPoublie',{email: this.email, toasted: this.$toasted});

                    this.$router.push({name:"auth"});
                }
            });
        }

    }

}

</script>
