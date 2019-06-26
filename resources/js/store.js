// On importe Vue et VueX
import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';
import VueAxios from 'vue-axios';
import { event } from 'vue-analytics';

// On utilise VueX et Axios, VueAnalytics
Vue.use(Vuex);
Vue.use(VueAxios, axios);

// On crée l'espace de State Storage
export const store = new Vuex.Store({
    state: {
      inscription_forcee:false,
      connexion_forcee:false,
      chatopen_force:0,
      pending: false,
      connected: !!localStorage.getItem('api_token_uir'),
      // Pour effectuer le suivi des champs grisés
      disabled:{gender:false,cin:false,country:false,city:false,address:false,niveau:false,annee_diplome:false,country_etudes:false,city_etudes:false,type_institut:false,systeme_etudes:false,specialites:false,etablissement:false},
      //connected: true,
      api_token: null,
      step_level: 0,
      steps: [false,false,false,false],
      lead_level: 0,
      preloading: false,

      // DEBUT Informations des étapes 0,1,2,3,4
      user: {id:null,uir_student:null,firstname:'',lastname:'',email:null, email_confirmed:0},
      profil: {sexe:null, cin:null, country:null, city:null,address:null,gsm:'', cndp:''},
      cursus: {niveau:null, annee_diplome:null, country_etudes:null, city_etudes:null, systeme_etudes:null, type_institut:null, etablissment:null,specialite:null},
      formations_choisies:[],
      myrdv_time_id:[],
      myforma_time_id:[],
      mydates_time_id: [],
      // FIN

      // Mes convocations du candidat
      mes_convocations:[],

      // DATA publique (Formations, Prérequis, RDV Dispo, Spécialités, Localisations et Année obtention diplôme)
       formations:[
      ],

      prerequis:[],

      liste_rdv_dispo:[],
      specialites: [],
      locations: [{country: '', cities: [], nationalite: ''}],


      years: [], // On génére automatiquement les années du SelectItem Années (Année obtention diplôme)
    },
    getters : {
        getPrerequis(state){
          return param => {
              return state.prerequis.find(el => el.formation_id === param);
          }
        },
        getMyFormations(state){
            return state.formations.filter(el => state.formations_choisies.indexOf(el.id) >= 0);
        },
        getCountries(state) {
            return state.locations.map(item => item.country);
        },
        getCities(state){
          return param => {
              return state.locations.find(el => el.country === param);
          };
        },
        getNationalites(state){
          return state.locations.map(item => item.nationalite);
        },
        getSpecialites(state){
          return param => {
          return state.specialites.filter(el => el.uirformation_id_map === param);
        }

        },
        getFormations(state){
          return param => {
              return state.formations.filter(el => el.niveau_min <= param);
          }
        },
        getDisposRDV(state){
          return param => {
              return state.liste_rdv_dispo.filter(el => param.indexOf(el.formation_id) >= 0);
          }
        }
    },
    actions: {
        // Action de forcer l'inscription
        tellFrocerInscription(context,payload){
          context.commit('forcer_inscription',payload);
        },
        // Action de forcer l'inscription
        tellFrocerConnexion(context,payload){
          context.commit('forcer_connexion',payload);
        },
        // Envoyer email MDP Oublié
        tellEmailMDPoublie(context,payload){
          axios.post('/email/envoyer-mdpoublie',{email: payload.email})
            .then(response => {

              payload.toasted.show('Email envoyé avec succès...', {
                        icon: 'check',
                        duration: 2500,
                        className: 'mytoast',
                        closeOnSwipe: true
                        });

            }).catch(error => {

              payload.toasted.show('E-mail utilisateur inexistant...', {
                        icon: 'clear',
                        duration: 2500,
                        closeOnSwipe: true
                        });
            });
        },

         // Envoyer email Vérifier votre email
        tellEmailVerifier(context,payload){
          axios.post('/email/envoyer-emailconfirmation',{email: payload.email})
            .then(response => {

             payload.toasted.show('Veuillez vérifier votre courrier e-mail...', {
                        icon: 'check',
                        duration: 2500,
                        className: 'mytoast',
                        closeOnSwipe: true
                        });

            }).catch(error => {
              payload.toasted.show('E-mail utilisateur inexistant...', {
                        icon: 'clear',
                        duration: 2500,
                        closeOnSwipe: true
                        });

            });
        },

        // Envoyer email Convocation créée
        tellEmailConvocationCreee(context){
          axios.post('/email/convocation-creee',{api_token: localStorage.getItem("api_token_uir")})
            .then(response => {
            }).catch(error => {
            });
        },

        // Connexion via UIR Etudiant
        tellUIRStudent(context,payload){

          payload.toaster.show('Connexion en cours...', {
                        icon: 'hourglass_empty',
                        duration: 2500,
                        className: 'mytoast',
                        closeOnSwipe: true
                        });

         context.commit('Login'); // Montrer le spinner

          axios.post('/api/auth365',{email: payload.email, password: payload.password})
          .then(response => {


            if(response.data.statut == 'identifiants_incorrects'){ // On notifie l'tulisateur que les identifiants sont incorrects

              payload.toaster.show('Identifiants incorrects...', {
                        icon: 'clear',
                        duration: 2500,
                        closeOnSwipe: true
                        });

              context.commit('LoginFailed');


            } else if(response.data.statut == 'inscrit_succes'){ // On connecte l'utilisateur et on dit qu'il est inscrit

              return new Promise(resolve => {

                localStorage.setItem("api_token_uir", response.data.user.api_token);

                context.commit('LoginSuccess', {user: response.data.user});

                 context.dispatch('tellGetUserInfo',{api_token: response.data.api_token,route: 'step1',router: payload.router});

                  event('Conversions', 'Inscription', 'uir', 1);

                resolve();

                payload.toaster.show('Inscription candidat réussie...', {
                        icon: 'check',
                        duration: 2500,
                        className: 'mytoast',
                        closeOnSwipe: true
                        });
              });

            } else if(response.data.statut == 'clone_inexistant'){  // On redirige l'utilisateur vers l'onglet d'inscription

              context.commit('LoginFailed');

              context.commit('forcer_inscription',true);

              payload.toaster.show('Etudiant UIR valide mais non existant dans notre liste. Inscrivez-vous svp.', {
                        icon: 'clear',
                        duration: 10000,
                        closeOnSwipe: true
                        });



            } else if(response.data.statut == 'a_connecter'){ // s'il faut juste connecter l'utilisateur


              return new Promise(resolve => {

              localStorage.setItem("api_token_uir", response.data.user.api_token);

              context.commit('LoginSuccess', {user: response.data.user});

              resolve();

              context.dispatch('tellGetUserInfo',{api_token: response.data.user.api_token,route: 'step1', router:payload.router});


              payload.toast.show('Connecté...', {
                        icon: 'check',
                        duration: 2500,
                        className: 'mytoast',
                        closeOnSwipe: true
                        });

              });

            }

            }).catch(error => {
              payload.toaster.show('Identifiants Office365 incorrects...', {
                        icon: 'clear',
                        duration: 2500,
                        closeOnSwipe: true
                        });

              context.commit('LoginFailed');

            });
        },
        //Rammener la liste des RDV disponibles
        tellGetDisposList(context){
          context.commit('GetDisposList');
        },
        // Effectuer l'inscription d'un utilisateur
        tellInscription(context,payload){

          context.commit('Login');

          payload.toaster.show('Inscription en cours...', {
                        icon: 'check',
                        duration: 4500,
                        className: 'mytoast',
                        closeOnSwipe: true
                        });

          axios.post('/api/inscription',{first_name: payload.first_name, last_name: payload.last_name, email: payload.email, password: payload.password, uir_student: 0})

            .then(response => {

                if(response.data.statut == 'existant'){

                  context.commit('forcer_connexion',true);

                  payload.toaster.show('Votre compte est pré-existant. Il vous suffit de vous connecter...', {
                        icon: 'clear',
                        duration: 4500,
                        closeOnSwipe: true
                        });

                  context.commit('LoginFailed');



                } else {

                return new Promise(resolve => {

                localStorage.setItem("api_token_uir", response.data.user.api_token);

                context.commit('LoginSuccess', {user: response.data.user});

                resolve();

                event('Conversions', 'Inscription', 'non_uir', 1);

                context.commit('saveUser',response.data.user);

                payload.router.push({name:'step1'});

                payload.toaster.show('Inscription candidat réussie...', {
                        icon: 'check',
                        duration: 2500,
                        className: 'mytoast',
                        closeOnSwipe: true
                        });


                setTimeout(() => {
                  context.dispatch('tellEmailVerifier',{email: payload.email,toasted: payload.toaster});
                }, 2000);


                });
                }


            }).catch(error => {

              if(error.response.status == 422) {

                context.commit('forcer_connexion',true);

                payload.toaster.show('Utilisateur déjà inscrit. Veuillez vous connecter.', {
                        icon: 'clear',
                        duration: 2500,
                        closeOnSwipe: true
                        });
              }
              context.commit('LoginFailed');

            })

          context.commit('Inscription',payload);
        },

        tellCreateYearsSelect(context,payload){
          context.commit('CreateYearsSelect',payload);
        },

        tellChangePassword(context,payload){

          axios.post('/api/candidat/modifer-mdp',{newpass: payload.newpass, api_token: localStorage.getItem("api_token_uir")})
          .then(response => {
            payload.toasted.show('Mot de passe changé avec succès', {
                        icon: 'check',
                        duration: 2500,
                        className: 'mytoast',
                        closeOnSwipe: true
                        });
          })
          .catch(error => {
            payload.toasted.show('Erreur. Veuillez vous déconnecter et réessayer', {
                        icon: 'clear',
                        duration: 2500,
                        closeOnSwipe: true
                        });
          });

        },
        tellSetApiToken(context){
          context.commit('SetApiToken')
        },
        tellChangeEmail(context,payload){
          axios.post('/api/candidat/modifer-email',{newemail: payload.newemail, api_token: localStorage.getItem("api_token_uir")})
          .then(response => {

            context.commit('changemyemail',{email:payload.newemail});

            payload.toasted.show('E-mail changé avec succès', {
                        icon: 'check',
                        duration: 2500,
                        className: 'mytoast',
                        closeOnSwipe: true
                        });
          })
          .catch(error => {
            payload.toasted.show('Erreur. E-mail déjà existant...', {
                        icon: 'clear',
                        duration: 2500,
                        closeOnSwipe: true
                        });
          });
        },
        tellNewConvocation(context){
          context.commit('NewConvocation');
        },
        tellGetMyConvocations(context){
          context.commit('GetMyConvocations');
        },
        tellPreloading(context,payload){
          context.commit('Preload',payload);
        },
        tellSaveProfile(context,payload){
          context.commit('SaveProfile',payload);
        },
        tellConnected(context,payload){
          context.commit('SaveConnected',payload);
        },
        tellSaveMyRDV(context,payload){
          context.commit('SaveRDV',payload);
        },
        tellChatForce(context,payload){
          context.commit('ChatForce',payload);
        },
        tellStepLevel(context,payload){
          context.commit('setStepLevel',payload);
        },
        tellStepDone(context,payload){

          if(payload == 0){
              event('Conversions', 'Etape1', 'accomplie', 1);
          } else if(payload == 1){
              event('Conversions', 'Etape2', 'accomplie', 1);
          } else if(payload == 2){
              event('Conversions', 'Etape3', 'accomplie', 1);
          } else if(payload == 3){
              event('Conversions', 'Etape4', 'accomplie', 1);
          }

          context.commit('setStepDone',payload);
        },
        tellGetSpecialitesList(context){
          context.commit('GetSpecialitesList');
        },
        tellUser(context,payload){
          context.commit('saveUser',payload);
        },
        tellSaveCursus(context,payload){
          context.commit('saveCursus',payload);
        },
        tellSaveMyRDV_DB(context,payload){
          context.commit('SaveMyRDV_DB',payload)
        },
        tellGetFormationsList(context){
          context.commit('GetFormationsList');
        },
        tellGetUserInfo(context,payload){

          axios.get('/api/candidat/infos-utilisateur?api_token=' + localStorage.getItem("api_token_uir"))

            .then(response => {

              var hours = 1; // Reset when storage is more than 24hours
              var now = new Date().getTime();
              var setupTime = localStorage.getItem('setupTime_uir');
              if (setupTime == null) {
                  localStorage.setItem('setupTime_uir', now)
              } else {
                  if(now-setupTime > hours*60*60*1000) {
                      localStorage.removeItem('api_token_uir');
                  }
              }

              context.commit('GetUserInfo',response);

              if(payload.route == 'step1'){

                payload.router.push({name:'step1'});

              }

              //console.log(response);

            }).catch(error => {

              payload.router.push({name:'auth'});

              localStorage.removeItem("api_token_uir");

              context.commit('connectfalse');

            });

        },
        tellSaveMyFormations(context,payload){
          context.commit('saveMyFormations',payload);
        },

        // DEBUT On ramène de la DATA (Liste villes, Formations, Ecoles...)
        tellGetLocations(context){
           axios.get('/api/localisations')
            .then(response => {
              context.commit('SetLocations',response.data);
            })
            .catch(error => {
                alert('Impossible de télécharger la liste des Pays et Villes. \n\n'+error);
            });
        },
        //FIN

        tellDisable(context){
          context.commit('DisableAll');
        },

        // DEBUT Traitement des statuts d'authentification
        tellConnect(context, payload) {

        localStorage.clear();

         context.commit('Login'); // Montrer le spinner
         context.dispatch('tellPreloading',true);

         payload.toast.show('Authentification...', {
                        icon: 'check',
                        duration: 4500,
                        className: 'mytoast',
                        closeOnSwipe: true
                        });

         if(payload.auth_type == 1){

            axios.post('/api/connexion',{email: payload.email, password: payload.password})

            .then(response => {

              return new Promise(resolve => {

              localStorage.setItem("api_token_uir", response.data.user.api_token);

              context.commit('LoginSuccess', {user: response.data.user});

              context.dispatch('tellGetUserInfo',{api_token: response.data.user.api_token,route: 'step1',router: payload.router});

              resolve();


              payload.toast.show('Connecté...', {
                        icon: 'check',
                        duration: 2500,
                        className: 'mytoast',
                        closeOnSwipe: true
                        });

              });

            }).catch(error => {

              payload.toast.show('Identifiants incorrects...', {
                        icon: 'clear',
                        duration: 2500,
                        closeOnSwipe: true
                        });

              context.commit('LoginFailed');

            })

         }
       },

       tellDisconnect(context,payload) {
        payload.toasted.show('Déconnexion...', {
                        icon : 'clear',
                        duration: 2500,
                        closeOnSwipe: true
                    });
        // On contact le backend pour supprimer l'API token de la BDD
            axios.post('/api/candidat/deconnexion', { api_token : localStorage.getItem("api_token_uir") })

            .then(response => {

              return new Promise(resolve => {
              payload.toasted.show('Déconnecté(e)...', {
                        icon : 'clear',
                        duration: 2500,
                        closeOnSwipe: true
                    });

                // On efface le token du LocalStorage
                localStorage.removeItem("api_token_uir");

                // On change de route avec VueRouter (inclus dans le Payload)
                payload.router.push({name:'auth'});

                // On change le statut dans le State du Store
                context.commit('Logout');

              });

            }).catch(error => {

              alert(error);

            })


       }
       // FIN
    },
    mutations: {
        connectfalse(state){
          state.connected = false;
        },
        // On False le ChatForce
        ChatForce(state,payload){
          state.chatopen_force = payload;
        },
        // On force l'ouverture du component Inscription
        forcer_inscription(state,payload){
          state.inscription_forcee = payload;
        },

        // On force l'ouverture du component Connexion
        forcer_connexion(state,payload){
          state.connexion_forcee = payload;
        },


        // On change l'email dans le store
        changemyemail(state,payload){
          state.user.email = payload.email;
        },
        // On affiche le Preloader ou non
        Preload(state,payload){
          state.preloading = payload;
        },
        // On ramène la liste de nos convocations
        GetMyConvocations(state){
          axios.get('/api/candidat/mes-convocations?api_token=' + localStorage.getItem('api_token_uir'))

            .then(response => {

              state.mes_convocations = response.data.convocations;

              state.preloading = false;

            }).catch(error => {

              alert(error);

            });
        },

        // On ramène la liste des RDV disponibles ainsi que nos RDV à nous
        GetDisposList(state){

          axios.get('/api/candidat/rdv-disponibles?api_token=' + localStorage.getItem('api_token_uir'))

            .then(response => {

              state.liste_rdv_dispo = response.data.data;

              state.preloading = false;

            }).catch(error => {

            });

        },
        // On ramène la liste des formations et des prérequis
        GetFormationsList(state){
          axios.get('/api/formations?api_token='+localStorage.getItem('api_token_uir'))

            .then(response => {
              //console.log(response.data.data.map(item => item.formations));
              state.formations = response.data.data.map(item => item.formations);

              state.prerequis = response.data.data.map(item => item.prerequis);

              state.preloading = false;

            }).catch(error => {
              alert('Impossible de télécharger la liste des formations UIR. \n\n' + error);
            });
        },
        // On ramène la liste des spécialités depuis la BDD
        GetSpecialitesList(state){

          axios.get('/api/specialites')

            .then(response => {

              state.specialites = response.data;

            }).catch(error => {
              alert('Impossible de télécharger la liste des spécialités. \n\n' + error);
            });

        },

        // On met à jour la liste des localisations dans notre store
        SetLocations(state,payload){
          state.locations = payload.data;
        },

        // Affecter au Store l'API key qui se trouve dans le Local Storage
        SetApiToken(state,payload){
          state.api_token = localStorage.getItem('api_token_uir');
        },
        // DEBUT Mutations des statuts d'authentification
        Login(state,payload) {
          state.pending = true;
        },

        LoginSuccess(state,payload) {
          state.connected = true;
          state.pending = false;
          state.api_token = localStorage.getItem("api_token_uir");

        },
        Logout(state) {
          state.step_level = 0;
          state.connected = false;
          state.api_token = null;
          state.user = {id:null,uir_student:null,firstname:null,lastname:null,email:null, email_confirmed:0};
          state.profil = {sexe:1, cin:null, country:null, city:null,address:null,gsm:null, cndp:null};
          state.cursus = {niveau:null, annee_diplome:null, country_etudes:null, city_etudes:null, systeme_etudes:null, type_institut:null, etablissment:null,specialite:null};
          state.formations_choisies = [];
          state.myrdv_time_id = [];
          state.steps = [false,false,false,false];
          state.preloading = false;
          state.disabled = {gender:false,cin:false,country:false,city:false,address:false,niveau:false,annee_diplome:false,country_etudes:false,city_etudes:false,type_institut:false,systeme_etudes:false,specialites:false,etablissement:false};

        },
        LoginFailed(state) {
          state.pending = false;
        },
        //FIN

        // Création des années dans le SlectItem (Année obtention diplôme)
        CreateYearsSelect(state,payload){
          state.years = payload;
        },

        SaveConnected(state,payload){
          state.connected = payload;
        },

        // DEBUT Gestion des Etapes en haut de page
        setStepLevel(state,payload) {
          state.step_level = payload;
        },
        setStepDone(state,payload){
          state.steps[payload] = true;
          var lead_level = state.steps.filter(Boolean).length;

          if(lead_level <= state.step_level){
          state.lead_level = lead_level;
            axios.post('/api/candidat/modifier-niveau', {api_token: state.api_token,niveau: lead_level})
            .then(response => {
            }).catch(error => {
              alert(error);
            })
          }
        },
        // FIN

        // DEBUT Gestion des paramètres
        ChangeEmail(state,payload){
          state.user.email = payload;
        },
        // FIN

        DisableAll(state){
          state.disabled = {gender:true,cin:true,country:true,city:true,address:true,niveau:true,annee_diplome:true,country_etudes:true,city_etudes:true,type_institut:true,systeme_etudes:true,specialites:true,etablissement:true};
        },

        // Inscription
        Inscription(state,payload){

        },

        // Etape 0
        saveUser(state,payload){
            state.user.uir_student = payload.uir_student;
            state.user.email = payload.email;
            state.user.firstname = payload.first_name;
            state.user.lastname = payload.last_name;
            state.user.id = payload.id;
            state.user.email_confirmed = 0;
            state.preloading = false;
        },

        // Etape 1
        SaveProfile(state,payload){
          state.profil.sexe = payload.sexe;
          state.profil.cin = payload.cin;
          state.profil.country = payload.country;
          state.profil.city = payload.city;
          state.profil.address = payload.address;
          state.profil.gsm = payload.gsm;


          axios.post('/api/candidat/modifier-profil',
            {
              'api_token': state.api_token,
              'gender': payload.sexe,
              'cin': payload.cin,
              'country_id': payload.country,
              'city_id': payload.city,
              'address': payload.address,
              'phone': payload.gsm
            })

            .then(response => {


            }).catch(error => {
              alert(error);
            });

        },

        // Etape 2
        saveCursus(state,payload){
          state.cursus.niveau = payload.niveau;
          state.cursus.annee_diplome = payload.annee_diplome;
          state.cursus.country_etudes = payload.country_etudes;
          state.cursus.city_etudes = payload.city_etudes;
          state.cursus.systeme_etudes = payload.systeme_etudes;
          state.cursus.type_institut = payload.type_institut;
          state.cursus.etablissment = payload.etablissment;
          state.cursus.specialite = payload.specialite;

          axios.post('/api/candidat/modifier-cursus',
            {
              'niveau': payload.niveau,
              'annee_diplome': payload.annee_diplome,
              'countryedu_id': payload.country_etudes,
              'cityedu_id': payload.city_etudes,
              'systemedu_id': payload.systeme_etudes,
              'typedu': payload.type_institut,
              'specialite_id': payload.specialite,
              'school': payload.etablissment,
              'api_token': state.api_token
            })

            .then(response => {
              state.preloading = false;

            }).catch(error => {
              alert(error);
            });

        },

        // Etape 3
        saveMyFormations(state,payload){
          state.formations_choisies = payload;

          axios.post('/api/candidat/modifier-mesformations',
            {
              'mes_formations': payload.join(),
              'api_token': state.api_token
            })

            .then(response => {


            }).catch(error => {
              alert(error);
            });
        },

        // Etape 4 - 1
        SaveRDV(state,payload){
          state.myrdv_time_id = payload;
        },

        //4 - 1 Spécial BDD
        SaveMyRDV_DB(state,payload){

          axios.post('/api/candidat/modifier-mesrdv',
            {
              'myrdv_time_id': payload.join(),
              'api_token': state.api_token
            })

            .then(response => {


            }).catch(error => {
              alert(error);
            });
        },

        // Etape 4 - 2
        NewConvocation(state){

          if(state.mes_convocations.length > 0 ){
            state.mes_convocations[state.mes_convocations.length-1].statut = false;
          }

          var today = new Date();
          var date = ('0' + today.getDate()).slice(-2)+'-'+('0' +(today.getMonth()+1)).slice(-2)+'-'+today.getFullYear();
          var time = today.getHours() + ":" + today.getMinutes();
          var dateTime = date+' '+time;

          state.mes_convocations.push({
            id:null,
            ref:state.user.id+'-UIR',
            url:'http://www.google.com/'+state.user.id+'-UIR-'+(state.mes_convocations.length + 1)+'.pdf',
            created_at:dateTime,
            statut:true
          })
        },

        // On rapporte les infos de l'utilisateur
        GetUserInfo(state,payload){


          state.lead_level = payload.data.user.lead_level;
          state.user = payload.data.user;
          state.preloading = false;

          if(state.lead_level > 0){
            state.steps = [true,false,false,false];
            state.profil = payload.data.profil;
          }

          if(state.lead_level  > 1){
            state.steps = [true,true,false,false];
            state.cursus = payload.data.cursus;
          }

          if(state.lead_level  > 2){
            state.steps = [true,true,true,false];
            state.formations_choisies = payload.data.formations_choisies;
          }

          if(state.lead_level > 3){
            state.steps = [true,true,true,true];
            state.myrdv_time_id = payload.data.myrdv_time_id;
            state.myforma_time_id = payload.data.myforma_time_id;
            state.mydates_time_id = payload.data.mydates_time_id;
            state.disabled = {gender:true,cin:true,country:true,city:true,address:true,niveau:true,annee_diplome:true,country_etudes:true,city_etudes:true,type_institut:true,systeme_etudes:true,specialites:true,etablissement:true};

          }



          // On affecte les champs à Disable
          if(state.user.uir_student == 1){

                if(state.profil.sexe != null){
                    state.disabled.gender = true;
                }

                if(state.profil.cin != null){
                    state.disabled.cin = true;
                }

                if(state.profil.country != null){
                    state.disabled.country = true;
                }

                if(state.profil.city != null){
                    state.disabled.city = true;
                }

                if(state.profil.address != null){
                    state.disabled.address = true;
                }

                if(state.cursus.niveau != null){
                    state.disabled.niveau = true;
                }

                if(state.cursus.annee_diplome != null){
                    state.disabled.annee_diplome = true;
                }

                if(state.cursus.country_etudes != null){
                    state.disabled.country_etudes = true;
                }

                if(state.cursus.city_etudes != null){
                    state.disabled.city_etudes = true;
                }

                if(state.cursus.type_institut != null){
                    state.disabled.type_institut = true;
                }

                if(state.cursus.systeme_etudes != null){
                    state.disabled.systeme_etudes = true;
                }

                if(state.cursus.specialite != null){
                    state.disabled.specialites = true;
                }

                if(state.cursus.etablissment != null){
                    state.disabled.etablissement = true;
                }
            }


        },
    }
  })