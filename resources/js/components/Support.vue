<template>
    <div class="mt-5 text-center">
        <h4 class="grand-titre2 text-uppercase text-center">Support</h4>
        <span class="pb-5">UIR est à votre écoute...</span>
        <div class="mt-5 col-md-8 offset-md-2">
            <p class="font-weight-light">Veuillez prendre contact avec notre agréable équipe de support dédiée au traitement des demandes de nos chers candidats.</p>
            <div class="row mt-5 mb-4 text-08">
                <div class="col-md-6 mb-4"><a href="tel:0530103000" class="nohref"><div class="btn_support d-flex align-items-center"><i class="material-icons text-green mr-2">phone_in_talk</i> Tél: 0530 10 30 00</div></a></div>
                <div class="col-md-6"><a href="mailto:concours@uir.ac.ma?Subject=Demande%20de%20renseignements" target="_blank" class="nohref"><div class="btn_support d-flex align-items-center"><i class="material-icons text-green mr-2">mail_outline</i> Concours@uir.ac.ma</div></a></div>
            </div>
            <div class="row pb-5 text-08">
                <div class="col-md-6 mb-4"><a href="#" class="nohref" @click.prevent="chatforce()"><div class="btn_support d-flex align-items-center" data-toggle="tooltip" title="Support ChatBot bientôt disponible..."><i class="material-icons text-green mr-2">chat_bubble_outline</i> ChatBot</div></a></div>

                <div class="col-md-6"><a href="http://www.uir.ac.ma" class="nohref" target="_blank"><div class="btn_support d-flex align-items-center"><i class="material-icons text-green mr-2">language</i> www.UIR.ac.ma</div></a></div>
            </div>
            <p class="font-weight-light text-left">
                <font class="text-green"><i class="material-icons">pin_drop</i> <b>Adresse:</b></font><br /> Parc Technopolis Rabat-Shore, Rocade Rabat-Salé<br /> 11 100 - Sala el Jadida<br />Maroc</p>
    </div></div>
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
        chatforce: function(){
            this.$store.dispatch('tellChatForce',1);
        },
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
