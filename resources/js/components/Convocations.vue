<template>
    <div>
    <div class="d-flex justify-content-center align-items-center pt-5" v-if="preloading">

        <svg class="spinner mt-5" width="40px" height="40px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
            <circle class="path_green" fill="none" stroke-width="8" stroke-linecap="round" cx="33" cy="33" r="23"></circle>

        </svg>
    </div>
    <div class="mt-5" v-else>
        <div class="text-left col-md-10 offset-md-1">
            <h4 class="grand-titre2 text-center mb-3 text-uppercase">Mes convocations créées <font class="text-green">({{store_mesconvocations.length}})</font>
            </h4>
            <h6 class="text-center font-weight-light line-2" :class="store_mesconvocations.length == 0 ? 'pb-3' : 'pb-5'">Veuillez vous présenter à vos rendez-vous de concours munis de votre <b class="text-green">convocation valide</b> obligatoire sous format Papier ou Numérique.</h6>
            <div class="convo_table mt-5" v-if="store_mesconvocations != null && store_mesconvocations.length != 0">
                <div class="row text-muted text-uppercase mb-4">
                    <div class="col-3">Référence</div>
                    <div class="col-4"><span class="d-none d-md-inline">Date de création</span><span class="d-md-none d-inline">Créée le</span></div>
                    <div class="col-2 text-center">Statut</div>
                </div>
                <div class="row convocation_item" v-for="convocation in store_mesconvocations" data-toggle="tooltip" :title="convocation.statut == true ? 'Convocation Valide' : 'Convocation Annulée'">
                    <div class="col-3"><a :href="'/api/candidat/voir-convocation/'+convocation.id+'?api_token=' + api_token" target="_blank">{{ convocation.ref }}</a></div>
                    <div class="col-4">{{ convocation.created_at }}</div>
                    <div class="col-2 text-center">
                        <i class="material-icons text-green" v-if="convocation.statut">check_circle</i>
                        <i class="material-icons text-danger" v-else>delete_forever</i>
                    </div>
                    <div class="col-3 d-flex align-items-center" v-if="convocation.statut"><i class="material-icons text-primary mr-2">file_download</i> <a :href="'/api/candidat/convocation-pdf/'+convocation.id+'?api_token=' + api_token" target="_blank" ><span class="d-none d-md-inline">Télécharger</span><span class="d-inline d-md-none">Voir</span></a></div>
                </div>
            </div>
            <div class="mt-3 d-flex justify-content-center" v-else>
                <div class="circle_noconvo">
                    <svg version="1.1" id="Calque_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="223.333px" height="273.333px" viewBox="0 0 223.333 273.333" enable-background="new 0 0 223.333 273.333" xml:space="preserve">
                        <g>
                            <rect y="17.91" fill="#FFFFFF" width="203" height="255" />
                            <g>
                                <path fill="#DBE1EC" d="M147.7,88.843c0,1.62-1.313,2.933-2.933,2.933H58.233c-1.62,0-2.933-1.313-2.933-2.933l0,0
            c0-1.62,1.313-2.933,2.933-2.933h86.533C146.387,85.91,147.7,87.223,147.7,88.843L147.7,88.843z" />
                                <path fill="#DBE1EC" d="M147.7,103.51c0,1.62-1.313,2.933-2.933,2.933H58.233c-1.62,0-2.933-1.313-2.933-2.933l0,0
            c0-1.62,1.313-2.933,2.933-2.933h86.533C146.387,100.577,147.7,101.89,147.7,103.51L147.7,103.51z" />
                                <path fill="#DBE1EC" d="M98.695,119.643c0,1.62-1.313,2.933-2.933,2.933H58.233c-1.62,0-2.933-1.313-2.933-2.933l0,0
            c0-1.62,1.313-2.933,2.933-2.933h37.528C97.382,116.71,98.695,118.023,98.695,119.643L98.695,119.643z" />
                            </g>
                            <g>
                                <path fill="#FFFFFF" d="M207.548,19.91l10.545-10.545c1.456-1.456,1.456-3.817,0-5.273c-1.456-1.456-3.817-1.456-5.273,0
            l-10.545,10.545L191.73,4.092c-1.456-1.456-3.817-1.456-5.273,0c-1.456,1.456-1.456,3.817,0,5.273l10.545,10.545l-10.545,10.545
            c-1.456,1.456-1.456,3.817,0,5.273c1.456,1.456,3.817,1.456,5.273,0l10.545-10.545l10.545,10.545c1.456,1.456,3.817,1.456,5.273,0
            c1.456-1.456,1.456-3.817,0-5.273L207.548,19.91z" />
                                <path fill="#DBE1EC" d="M215.457,3c0.954,0,1.908,0.364,2.636,1.092c1.456,1.456,1.456,3.817,0,5.273L207.548,19.91l10.545,10.545
            c1.456,1.456,1.456,3.817,0,5.273c-0.728,0.728-1.682,1.092-2.636,1.092c-0.954,0-1.908-0.364-2.636-1.092l-10.545-10.545
            L191.73,35.728c-0.728,0.728-1.682,1.092-2.636,1.092c-0.954,0-1.908-0.364-2.636-1.092c-1.456-1.456-1.456-3.817,0-5.273
            l10.545-10.545L186.457,9.365c-1.456-1.456-1.456-3.816,0-5.273C187.185,3.364,188.139,3,189.093,3
            c0.954,0,1.908,0.364,2.636,1.092l10.545,10.545L212.82,4.092C213.548,3.364,214.502,3,215.457,3 M215.457,0
            c-1.797,0-3.487,0.7-4.757,1.97l-8.424,8.424l-8.424-8.424C192.58,0.7,190.891,0,189.093,0s-3.487,0.7-4.758,1.97
            c-1.271,1.271-1.971,2.961-1.971,4.758c0,1.797,0.7,3.487,1.971,4.758l8.424,8.424l-8.424,8.424c-2.623,2.623-2.623,6.892,0,9.515
            c1.271,1.271,2.96,1.971,4.758,1.971c1.797,0,3.487-0.7,4.758-1.971l8.424-8.424l8.424,8.424c1.271,1.271,2.96,1.971,4.758,1.971
            c1.797,0,3.487-0.7,4.758-1.971c2.623-2.623,2.623-6.892,0-9.515l-8.424-8.424l8.424-8.424c1.271-1.271,1.971-2.96,1.971-4.758
            c0-1.797-0.7-3.487-1.971-4.758C218.943,0.7,217.254,0,215.457,0L215.457,0z" />
                            </g>
                        </g>
                    </svg>
                </div>
            </div>
            <div v-if="store_mesconvocations.length == 0" class="text-center">
                    <h6 class="font-weight-light text-uppercase mb-4">Aucune convocation créée</h6>
                    <button class="btn btn-primary" @click="start()">Créez votre convocation</button>
            </div>
            <div class="text-right mt-3" v-else>
                
                    <button class="btn btn-secondary" @click="newconvo()">Nouvelle convocation</button>
            </div>
        </div>
    </div></div>
</template>
<script>
export default {
    props: [],

    data() {
        return {
            api_token:''

        }
    },

    computed: {
        store_mesconvocations() {
            return this.$store.state.mes_convocations;
        },

        preloading(){
            return this.$store.state.preloading;
        },
    },

    mounted: function() {
        this.api_token = localStorage.getItem('api_token_uir');
    },

    methods: {
        start: function(){
            this.$store.dispatch('tellStepLevel', 1);
            this.$router.push({ name: 'step1'});
        },

        newconvo: function(){
            this.$store.dispatch('tellStepLevel', 3);
            this.$router.push({ name: 'step3'});
        }


    }

}

</script>
