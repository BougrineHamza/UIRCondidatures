// Gestion des visibilités de configuration
Vue.config.devtools = false;
Vue.config.debug = false;
Vue.config.silent = true;

// On importe Vue et VueRouter et Vee-Validate,  V-Select
import Vue from 'vue';
import VueRouter from 'vue-router';
import frValidate from 'vee-validate/dist/locale/fr';
import VeeValidate, { Validator } from 'vee-validate';
import VueSelect from 'vue-cool-select';
import Toasted from 'vue-toasted';
import IdleVue from 'idle-vue';
import VueAnalytics from 'vue-analytics';
import Vuetify from 'vuetify';
// Translation provided by Vuetify (javascript)
import fr from 'vuetify/es5/locale/fr';

// On importe le State Storage, GAnalytics dans notre App pour pouvoir Emettre et Lire les infos
import {store} from "./store";

// On Importe les différents composants qu'on va appeler dans Vue Router
import Auth from './components/Auth.vue';
import Step1 from './components/Step1.vue';
import Step2 from './components/Step2.vue';
import Step3 from './components/Step3.vue';
import Step4 from './components/Step4.vue';
import Done from './components/Done.vue';
import Convocations from './components/Convocations.vue';
import Mdp from './components/Mdp.vue';
import Params from './components/Params.vue';
import Support from './components/Support.vue';

import Candidats from './components/Admin/Candidats.vue';
import Ecoles from './components/Admin/Ecoles.vue';
import Formations from './components/Admin/Formations.vue';
import Jurys from './components/Admin/Jurys.vue';
import Entretiens from './components/Admin/Entretiens.vue';
import Specialites from './components/Admin/Specialites.vue';
import Villes from './components/Admin/Villes.vue';
import Admins from './components/Admin/Admins.vue';
import admin_auth from './components/Admin/AdminAuth.vue';

//import axios , i will use it in the middleware i created in router to authorize against admin
import axios from 'axios';

//On enregistre des composants CANDIDATS
Vue.component('app-layout', require('./components/Layout.vue'));
Vue.component('app-steps', require('./components/Steps.vue'));
Vue.component('app-sidebar', require('./components/Sidebar.vue'));
Vue.component('app-auth', require('./components/Auth.vue'));
Vue.component('input-phone', require('./components/Input-phone.vue'));

//On enregistre des composants ADMIN
Vue.component('candidats', require('./components/Admin/Candidats.vue'));
Vue.component('ecoles', require('./components/Admin/Ecoles.vue'));
Vue.component('formations', require('./components/Admin/Formations.vue'));
Vue.component('jurys', require('./components/Admin/Jurys.vue'));
Vue.component('entretiens', require('./components/Admin/Entretiens.vue'));
Vue.component('specialites', require('./components/Admin/Specialites.vue'));
Vue.component('villes', require('./components/Admin/Villes.vue'));
Vue.component('admins', require('./components/Admin/Admins.vue'));
// Vue.component('admin_auth', require('./components/Admin/AdminAuth.vue'));



// Attacher l'events hub pour la déconnexion automatique sur IDLE
const eventsHub = new Vue();


Vue.use(VueRouter);
Vue.use(VeeValidate, {events: 'custom'}); // quand est-ce qu'on déclanche la validation (change,blur,...)
Vue.use(VueSelect, {theme: 'bootstrap'}); //bootstrap ou material-design
Vue.use(Toasted, {
    singleton: true // 1 Message Toast à la fois
});
Vue.use(IdleVue, {
  eventEmitter: eventsHub,
  idleTime: 5*60*1000
});
Vue.use(Vuetify, {
    lang: {
        locales: { fr },
        current: 'fr'
    }
})


// Enregistrement de toasts définis pour distribution globale
Vue.toasted.register('saved', 'Enregistré...', {
                icon : 'check',
                duration: 2200,
                className: 'mytoast toasted-radius',
                closeOnSwipe: true
});

Vue.toasted.register('step_left', 'Renseigner cette étape svp...', {
                icon : 'clear',
                duration: 2200,
                className: 'toasted-radius',
                closeOnSwipe: true
});

Vue.toasted.register('not_connected', 'Vous devez être authentifié...', {
                icon : 'clear',
                duration: 2200,
                className: 'toasted-radius',
                closeOnSwipe: true
});

// On met le Validator en français
Validator.localize('fr',frValidate);


// On crée nos Routes par Vue Router
const router = new VueRouter({
    mode: 'history',
    // base: '/public',
    routes: [
    	{path: '/', component: Auth, name:'auth'},
        {path: '/connexion', component: Auth, name:'connexion'},
    	{path: '/etape1', component: Step1, name:'step1'},
    	{path: '/etape2', component: Step2, name:'step2'},
    	{path: '/etape3', component: Step3, name:'step3'},
    	{path: '/etape4', component: Step4, name:'step4'},
        {path: '/convocation-envoyee', component: Done, name:'done'},
        {path: '/mes-convocations', component: Convocations, name:'mes-convocations'},
        {path: '/motdepasse-oublie', component: Mdp, name:'mdp-oublie'},
        {path: '/parametres', component: Params, name:'params'},
        {path: '/support', component: Support, name:'support'},

        {path: '/gestion/candidats', component: Candidats, name:'candidats', meta: { requiresAuthAdmin: true }},
        {path: '/gestion/ecoles', component: Ecoles, name:'ecoles', meta: { requiresAuthAdmin: true }},
        {path: '/gestion/formations', component: Formations, name:'formations', meta: { requiresAuthAdmin: true }},
        {path: '/gestion/jurys', component: Jurys, name:'jurys', meta: { requiresAuthAdmin: true }},
        {path: '/gestion/entretiens', component: Entretiens, name:'entretiens', meta: { requiresAuthAdmin: true }},
        {path: '/gestion/specialites', component: Specialites, name:'specialites', meta: { requiresAuthAdmin: true }},
        {path: '/gestion/villes', component: Villes, name:'villes', meta: { requiresAuthAdmin: true }},
        {path: '/gestion/admins', component: Admins, name:'admins', meta: { requiresAuthAdmin: true }},
        {path: '/gestion/connexion', component: admin_auth, name:'admin-login'}
    ]
});

//instead of Middlewaring in back-end, we will use a guard on each route on front-end
//for /gestion/{{any route}}
router.beforeResolve((to, from, next) => {
    //if it doesnt requiresAuthAdmin pass
    if(!to.meta.requiresAuthAdmin){next();return;};
    //else let's put a guard auth_admin here, shall we?
    let api_token=to.query.api_token;
    axios.get('/authAdmin?api_token=' + api_token).then(response => {
        response.data.Authorized ? next() : next('/gestion/connexion');
    }).catch(error => {alert(error);});
})

// On active Vue Analytics après le Router
Vue.use(VueAnalytics, {
  id: 'UA-137712231-1',
  router
});


// On ajoute une directive pour utiliser les Tooltips de Bootstrap ave JQuery
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// On insère tout dans le DIV #App dans app.blade.php avec notre Router et notre Storage
export const app = new Vue({
    el: '#app',
    router,
    store,
    onIdle() {
        // On déconnecte automatique la session après un certain temps IDLE
        if(this.$store.state.connected == true) {
            this.$store.dispatch('tellDisconnect',{router: this.$router, toasted:this.$toasted});
        }
    },
    data:()=>({
        drawer: true,
        location:window.location
    }),
    methods:{
        deconnexion(api_token){
            axios.post('/gestion/deconnexion',{api_token}).then(response=>{
                response.data.deconnexion?this.$router.push('/gestion/connexion'): alert('Tu n\'est pas encore deconnectez , problem de connexion') ;
            }).catch(error=>{
                alert(error);
            });
        }
    }
});


