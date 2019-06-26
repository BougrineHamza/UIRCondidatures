<template>
    <div class="container-fluid full-h position-relative px-0">

        <div v-if="user_confirmed !== undefined && user_confirmed != null && user_confirmed.email_confirmed == 0 && user_confirmed.email != null && connected == true" class="w-100 font-weight-light bg-muted validatemail py-2 px-3 d-flex align-items-center justify-content-between">
            <div>
            <span class="small mr-2 font-weight-light">Veuillez confirmer votre e-mail</span> <span class="badge badge-pill badge-info px-3 py-2 font-weight-light">{{ user_confirmed.email }}</span>
            </div>
            <div ><span class="badge badge-pill badge-dark font-weight-light px-3 py-2 sendit" @click="envoyer_confirmation()">Renvoyer e-mail<font class="d-md-inline d-none"> de confirmation</font></span></div>
        </div>

        <div class="row no-gutters" >
             <div class="chat-container">
                 <div class="chat" :class="chatopen ? 'chatopen':''" @click="openchat(1)"><i class="material-icons main_icon">chat_bubble_outline</i><span class="font-weight-light text-center"><i class="material-icons" style="font-size:2em;">chat</i><br />ChatBot bientôt disponible...</span>

                 </div>

                 <div class="rounded-circle close_chat" @click="openchat(0)"><i class="material-icons">clear</i></div>

            </div>
            <div id="separator" class="col-md-7 separator pt-2 full-h position-relative">
                <div class="pl-2"><img src="images/uir-logo.png" style="width: 190px;"></div>
                <div id="mymenu" class="d-flex align-items-center justify-content-end px-0 dropdown"><a href="#" class="dropdown" data-toggle="dropdown"><i class="material-icons burger">menu</i></a>
                    <div id="menu-inside" class="dropdown-menu dropdown-menu-right" :class="{'dropdown-menu-disconnected':!connected}">
                        <a href="#" class="dropdown-item" v-if="!connected" @click.prevent="goto('auth')" ><i class="material-icons mr-1">person</i>&nbsp;Authentification</a>
                        <a href="#" @click.prevent="goto('mes-convocations')" class="dropdown-item" v-if="connected"><i class="material-icons mr-1"  v-if="connected">alarm</i>&nbsp;Mes convocations</a><a href="#" class="dropdown-item" @click.prevent="goto('support')"><i class="material-icons mr-1">help_outline</i>&nbsp;Support</a><a href="#" class="dropdown-item" v-if="connected" @click.prevent="goto('params')"><i class="material-icons mr-1">settings</i>&nbsp;Paramètres</a>
                        <a href="#" @click.prevent="disconnect()" class="dropdown-item" v-if="connected"><i class="material-icons mr-1">remove_circle</i>&nbsp;Déconnexion</a>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-3 mb-5">
                    <router-view></router-view>
                </div>
                <div class="p-2"></div>
                <div class="footer pl-2"><span>Tous droits réservés - Université Internationale de Rabat</span></div>
            </div>
            <div id="rightpanel" class="col px-5 d-none d-md-block right-panel">
                <app-steps></app-steps>
                <app-sidebar></app-sidebar>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: [],

    data() {
        return {
            years:[],
            chatopen: 0,
        }
    },

    mounted: function() {

        var today = new Date();

        var toyear = Number(today.getFullYear());

        this.years = [toyear,toyear-1,toyear-2,toyear-3,toyear-4,toyear-5,toyear-6,toyear-7,toyear-8,'Autre'];

        this.$store.dispatch('tellCreateYearsSelect',this.years);

    },
    computed: {
        connected(){
            return this.$store.state.connected;
        },

        user_confirmed(){
            return this.$store.state.user;
        },

        chatopen_force(){
            return this.$store.state.chatopen_force;
        }

    },
    watch: {
        chatopen_force(newVal,oldVal){
                if(newVal == 1){
                    this.openchat(1);
                }
        }
    },

    methods: {
        openchat: function(val){
            if(val == 1){
                this.chatopen = 1;
            } else {
                this.chatopen = 0;
                this.$store.dispatch('tellChatForce',0);
            }
        },
        goto: function(where){

            if(where == 'mes-convocations'){
                this.$store.dispatch('tellGetMyConvocations');
            }
            this.$router.push({name: where});

        },
        disconnect: function(){
            this.$store.dispatch('tellDisconnect',{router: this.$router, toasted:this.$toasted});
        },
        envoyer_confirmation: function(){
            this.$store.dispatch('tellEmailVerifier',{email: this.user_confirmed.email, toasted:this.$toasted});

            this.$toasted.show('E-mail de confirmation envoyé...', {
                        icon: 'check',
                        duration: 2500,
                        closeOnSwipe: true
                        });
        }

    }

}

</script>
