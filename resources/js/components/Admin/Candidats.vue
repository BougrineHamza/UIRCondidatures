<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="text-uppercase font-weight-light text-green text-right">
                <span>
                    {{admin.first_name}} {{admin.last_name}}
                </span>
                <img :src="'/images/'+admin.uirecole.logo_path" style="width:90px;"
                        v-if="admin.uirecole_id != 0 && admin.uirecole" class="ml-4" />
            </h2>
    </div>
    <section class="gp_table elevation-3 px-2 py-3" style="background: white !important;">
      <v-toolbar flat color="white">
        <v-toolbar-title>Candidats ({{mes_candidats.length }})</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-text-field v-model="search" append-icon="search" label="Rechercher" single-line hide-details>
        </v-text-field>
        <v-spacer></v-spacer>
        <v-btn color="primary" dark class="mb-2" @click="toxlsx()">Exporter en Excel</v-btn>
      </v-toolbar>
      <v-data-table :headers="headers" :items="mes_candidats" :search="search" disable-initial-sort>
        <template v-slot:items="props">
          <td>{{ props.item.id }}</td>
          <td>
            <a href="#" @click.prevent="fiche_candidat(props.item.id)" class="text-capitalize">{{ props.item.last_name }}</a>
          </td>
          <td>
            <a href="#" @click.prevent="fiche_candidat(props.item.id)" class="text-capitalize">{{ props.item.first_name }}</a>
          </td>
          <td>
            <ul class="list-unstyled mb-0">
              <li v-for="time in props.item.concourtime">
                {{ time.uirformation?time.uirformation.titre:'' }}</li>
            </ul>
          </td>
          <td>
            <span v-if="props.item.convocation"
                            class="d-flex align-items-center">{{ props.item.convocation.length }} créée(s)</span>
          </td>
          <td>
            <span v-if="props.item.stat">{{ props.item.stat.lead_level }}/4</span>
          </td>
          <td>{{ props.item.created_at }}</td>
        </template>
        <template v-slot:no-data>
          <center>
          <v-btn color="primary" @click="initialize" class="my-5">
            <svg class="spinner mr-4" width="10px" height="10px" viewBox="0 0 66 66"
                      xmlns="http://www.w3.org/2000/svg">
                      <circle class="path" fill="none" stroke-width="22" stroke-linecap="round" cx="33" cy="33" r="23">
                      </circle>
            </svg> Patienter svp...</v-btn>
          </center>
        </template>
      </v-data-table>
    </section>
    <div class="modal left fade" style="padding-right: 15px;" tabindex="-1" role="dialog" id="fiche">
      <div class="fiche-modal modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Fiche candidat</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-0">
            <v-tabs centered color="rgb(28, 150, 173)" dark icons-and-text>
              <v-tabs-slider color="white"></v-tabs-slider>
              <v-tab href="#tab-Utilisateur"> Utilisateur <v-icon>person</v-icon>
              </v-tab>
              <v-tab href="#tab-Profil"> Profil <v-icon>assignment_ind</v-icon>
              </v-tab>
              <v-tab href="#tab-Cursus"> Cursus <v-icon>work</v-icon>
              </v-tab>
              <v-tab href="#tab-Entretiens"> Entretiens <v-icon>business_center</v-icon>
              </v-tab>
              <v-tab href="#tab-Convocations"> Convocations <v-icon>chat_bubble</v-icon>
              </v-tab>
              <v-tab href="#tab-Statistiques"> Statistiques <v-icon>trending_up</v-icon>
              </v-tab>
              <v-tab-item class="p-3" value="tab-Utilisateur">
                <v-container grid-list-lg>
                  <v-flex xs10 offset-xs1 offset-md0 md5 mb-5>
                    <v-layout text-xs-center text-md-left row wrap align-center class="profile-info elevation-3">
                      <v-flex md4>
                        <v-icon class="v-icon__profile">person_pin</v-icon>
                      </v-flex>
                      <v-flex md8>
                        <h4>{{ candidat.last_name }} <span
                                                        class="body-2">{{candidat.first_name }}</span></h4>
                        <h5>
                                                    <v-icon>school</v-icon> Etudiant UIR: <span
                                                        v-if="candidat.uir_student" class="text-green">Oui</span><span
                                                        class="text-danger" v-else>Non</span>
                                                </h5>
                      </v-flex>
                    </v-layout>
                  </v-flex>
                  <v-layout row wrap text-xs-center>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>fingerprint</v-icon>
                        <h4 class="font-weight-bold">ID Candidat</h4>
                        <span class="subheading">{{ candidat.id }}</span>
                      </div>
                    </v-flex>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>mail_outline</v-icon>
                        <h4 class="font-weight-bold">Email</h4>
                        <span class="subheading">{{ candidat.email }}</span>
                      </div>
                    </v-flex>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>access_time</v-icon>
                        <h4 class="font-weight-bold">Date inscription</h4>
                        <span class="subheading">{{ candidat.created_at && GetFormattedDate(candidat.created_at) }}</span>
                      </div>
                    </v-flex>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>spellcheck</v-icon>
                        <h4 class="font-weight-bold">E-mail confirmé</h4>
                        <span class="subheading" v-if="candidat.email_confirmed">Oui</span><span
                                                    v-else>Non</span>
                      </div>
                    </v-flex>
                  </v-layout>
                  <v-layout class="mt-4" v-if="forma_choisies != null && forma_choisies.length > 0">
                    <v-flex md12>
                      <h3>Formations choisies <font class="text-green">({{forma_choisies.length}})</font></h3>
                      <h5 v-for="forma in forma_choisies"><i class="text-green material-icons">check</i> {{ forma.titre }}</h5>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-tab-item>
              <v-tab-item class="p-3" value="tab-Profil">
                <v-container grid-list-lg>
                  <v-layout row wrap text-xs-center v-if="candidat.profil">
                    <v-flex md4>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>accessibility_new</v-icon>
                        <h4 class="font-weight-bold">Sexe</h4>
                        <span class="subheading"><span
                                                        v-if="candidat.profil.gender">Homme</span><span
                                                        v-else>Femme</span></span>
                      </div>
                    </v-flex>
                    <v-flex md4>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>assignment_ind</v-icon>
                        <h4 class="font-weight-bold">CIN</h4>
                        <span class="subheading">{{ candidat.profil.cin }}</span>
                      </div>
                    </v-flex>
                    <v-flex md4>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>language</v-icon>
                        <h4 class="font-weight-bold">Pays</h4>
                        <span class="subheading">{{ candidat.profil.country_id }}</span>
                      </div>
                    </v-flex>
                    <v-flex md4>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>place</v-icon>
                        <h4 class="font-weight-bold">Ville</h4>
                        <span class="subheading">{{ candidat.profil.city_id }}</span>
                      </div>
                    </v-flex>
                    <v-flex md4>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>my_location</v-icon>
                        <h4 class="font-weight-bold">Adresse</h4>
                        <span class="subheading">{{ candidat.profil.address }}</span>
                      </div>
                    </v-flex>
                    <v-flex md4>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>mobile_friendly</v-icon>
                        <h4 class="font-weight-bold">GSM</h4>
                        <span class="subheading">{{ candidat.profil.phone }}</span>
                      </div>
                    </v-flex>
                  </v-layout>
                  <v-layout row wrap text-xs-center justify-center align-center style="height:50vh" v-else>
                    <span class="display-2">Etape non renseignée...</span>
                  </v-layout>
                </v-container>
              </v-tab-item>
              <v-tab-item class="p-3" value="tab-Cursus">
                <v-container grid-list-lg>
                  <v-layout row wrap text-xs-center v-if="candidat.cursus">
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>local_library</v-icon>
                        <h4 class="font-weight-bold"> Niveau d'études</h4>
                        <span class="subheading">{{ candidat.cursus.niveau }}</span>
                      </div>
                    </v-flex>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>calendar_today</v-icon>
                        <h4 class="font-weight-bold">Année diplôme</h4>
                        <span class="subheading">{{ candidat.cursus.annee_diplome }}</span>
                      </div>
                    </v-flex>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>language</v-icon>
                        <h4 class="font-weight-bold">Pays d'études</h4>
                        <span class="subheading">{{ candidat.cursus.countryedu_id }}</span>
                      </div>
                    </v-flex>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>place</v-icon>
                        <h4 class="font-weight-bold">Ville</h4>
                        <span class="subheading">{{ candidat.cursus.cityedu_id }}</span>
                      </div>
                    </v-flex>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>language</v-icon>
                        <h4 class="font-weight-bold"> Système d'études</h4>
                        <span class="subheading">{{ candidat.cursus.systemedu_id }}</span>
                      </div>
                    </v-flex>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>account_balance</v-icon>
                        <h4 class="font-weight-bold">Type organisme</h4>
                        <span class="subheading">{{ candidat.cursus.typedu }}</span>
                      </div>
                    </v-flex>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>card_travel</v-icon>
                        <h4 class="font-weight-bold">Spécialité</h4>
                        <span class="subheading">{{ candidat.cursus.specialite_id }}</span>
                      </div>
                    </v-flex>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>school</v-icon>
                        <h4 class="font-weight-bold"> Etablissement</h4>
                        <span class="subheading">{{ candidat.cursus.school }}</span>
                      </div>
                    </v-flex>
                  </v-layout>
                  <v-layout row wrap text-xs-center justify-center align-center style="height:50vh" v-else>
                    <span class="display-2">Etape non renseignée...</span>
                  </v-layout>
                </v-container>
                <!-- <div class="row pt-4" v-if="candidat.cursus">
                                    <div class="col-md-6">
                                        <i class="text-green material-icons mr-1">local_library</i> Niveau d'études:
                                        <b v-if="candidat.cursus">{{ candidat.cursus.niveau }}</b>
                                        <i class="text-green material-icons mr-1">calendar_today</i>Année diplôme:
                                        <b v-if="candidat.cursus">{{ candidat.cursus.annee_diplome }}</b>
                                        <i class="text-green material-icons mr-1">language</i>Pays d'études:
                                        <b v-if="candidat.cursus">{{ candidat.cursus.countryedu_id }}</b>
                                        <i class="text-green material-icons mr-1">place</i>Ville:
                                        <b v-if="candidat.cursus">{{ candidat.cursus.cityedu_id }}</b>
                                        <i class="text-green material-icons mr-1">language</i> Système d'études:
                                        <b v-if="candidat.cursus">{{ candidat.cursus.systemedu_id }}</b>
                                        <i class="text-green material-icons mr-1">account_balance</i>Type organisme:
                                        <b v-if="candidat.cursus">{{ candidat.cursus.typedu }}</b>
                                        <i class="text-green material-icons mr-1">card_travel</i>Spécialité:
                                        <b v-if="candidat.cursus">{{ candidat.cursus.specialite_id }}</b>
                                        <i class="text-green material-icons mr-1">school</i> Etablissement:
                                        <b v-if="candidat.cursus">{{ candidat.cursus.school }}</b>
                                    </div>
                                </div> -->
              </v-tab-item>
              <v-tab-item class="p-3" value="tab-Entretiens">
                <div v-if="candidat.concourtime && candidat.concourtime[0]">
                  <v-container grid-list-lg v-for="(concour,index) in candidat.concourtime" :key="concour.id" :class="index!=0&&'pt-0'" pb-0 >
                    <v-layout row wrap text-xs-center>
                      <v-flex md4>
                        <div class="info-item elevation-3 py-4 px-2">
                          <v-layout row wrap>
                            <v-flex xs4>
                              <v-icon>fingerprint</v-icon>
                            </v-flex>
                            <v-flex xs8 text-xs-left>
                              <h4 class="font-weight-bold">ID entretien</h4>
                              <span class="subheading">{{ concour.id}}</span>
                            </v-flex>
                          </v-layout>
                        </div>
                      </v-flex>
                      <v-flex md4>
                        <div class="info-item elevation-3 py-4 px-2">
                          <v-layout row wrap>
                            <v-flex xs4>
                              <v-icon>calendar_today</v-icon>
                            </v-flex>
                            <v-flex xs8 text-xs-left>
                              <h4 class="font-weight-bold">Réservé le</h4>
                              <span class="subheading">{{ concour.updated_at &&GetFormattedDate(concour.updated_at)}}</span>
                            </v-flex>
                          </v-layout>
                        </div>
                      </v-flex>
                      <v-flex md4>
                        <div class="info-item elevation-3 py-4 px-2">
                          <v-layout row wrap>
                            <v-flex xs4>
                              <v-icon>local_library</v-icon>
                            </v-flex>
                            <v-flex xs8 text-xs-left>
                              <h4 class="font-weight-bold">Formation</h4>
                              <span class="subheading">{{concour.uirformation.titre}}</span>
                            </v-flex>
                          </v-layout>
                        </div>
                      </v-flex>
                      <v-flex md3>
                        <div class="info-item elevation-3 py-4 px-2">
                          <v-layout row wrap>
                            <v-flex xs4>
                              <v-icon>person</v-icon>
                            </v-flex>
                            <v-flex xs8 text-xs-left>
                              <h4 class="font-weight-bold">Jury</h4>
                              <span class="subheading">{{ concour.jury_id}}</span>
                            </v-flex>
                          </v-layout>
                        </div>
                      </v-flex>
                      <v-flex md3>
                        <div class="info-item elevation-3 py-4 px-2">
                          <v-layout row wrap>
                            <v-flex xs4>
                              <v-icon>calendar_today</v-icon>
                            </v-flex>
                            <v-flex xs8 text-xs-left>
                              <h4 class="font-weight-bold">Date</h4>
                              <span class="subheading">{{ concour.concourdate.date_concours && GetFormattedDate(concour.concourdate.date_concours)}}</span>
                            </v-flex>
                          </v-layout>
                        </div>
                      </v-flex>
                      <v-flex md3>
                        <div class="info-item elevation-3 py-4 px-2">
                          <v-layout row wrap>
                            <v-flex xs4>
                              <v-icon>access_time</v-icon>
                            </v-flex>
                            <v-flex xs8 text-xs-left>
                              <h4 class="font-weight-bold">Heure</h4>
                              <span class="subheading">{{ concour.time}}</span>
                            </v-flex>
                          </v-layout>
                        </div>
                      </v-flex>
                      <v-flex md3>
                        <div class="info-item elevation-3 py-4 px-2">
                          <v-layout row wrap>
                            <v-flex xs4>
                              <v-icon>school</v-icon>
                            </v-flex>
                            <v-flex xs8 text-xs-left>
                              <h4 class="font-weight-bold">Ecole</h4>
                              <span class="subheading">{{concour.uirformation.uirecole.titre}}</span>
                            </v-flex>
                          </v-layout>
                        </div>
                      </v-flex>
                    </v-layout>
                    <v-divider class="my-5" v-if="index + 1 < candidat.concourtime.length"></v-divider>
                  </v-container>
                </div>
                <v-container row wrap text-xs-center justify-center align-center style="height:50vh" v-else>
                  <span class="display-2">Etape non renseignée...</span>
                </v-container>
              </v-tab-item>
              <v-tab-item class="p-3" value="tab-Convocations">
                <div v-if="candidat.convocation && candidat.convocation[0]">
                  <v-container grid-list-lg v-for="(convocation,index) in candidat.convocation" :key="convocation.id" :class="index!=0&&'pt-0'" pb-0>
                    <v-layout row wrap text-xs-center>
                      <v-flex md4>
                        <v-flex xs12>
                          <div class="info-item elevation-3 py-4 px-2">
                            <v-layout row wrap>
                              <v-flex xs4>
                                <v-icon>language</v-icon>
                              </v-flex>
                              <v-flex xs8 text-xs-left>
                                <h4 class="font-weight-bold">Réf convocation</h4>
                                <span class="subheading">{{ convocation.ref}}</span>
                              </v-flex>
                            </v-layout>
                          </div>
                        </v-flex>
                        <v-flex xs12>
                          <div class="info-item elevation-3 py-4 px-2">
                            <v-layout row wrap>
                              <v-flex xs4>
                                <v-icon>link</v-icon>
                              </v-flex>
                              <v-flex xs8 text-xs-left>
                                <h4 class="font-weight-bold">Lien</h4>
                                <span class="subheading"><a :href="'convocation?api_token='+api_token+'&id='+convocation.id" target="_blank">Voir convocation</a></span>
                              </v-flex>
                            </v-layout>
                          </div>
                        </v-flex>
                      </v-flex>
                      <v-flex md4>
                        <div class="info-item elevation-3 py-4 px-2">
                            <v-icon style="font-size:7rem;">fingerprint</v-icon>
                            <h2 class="font-weight-bold">ID convocation</h2>
                            <span class="display-1">{{ convocation.id}}</span>
                        </div>
                      </v-flex>
                      <v-flex md4>
                        <v-flex xs12>
                          <div class="info-item elevation-3 py-4 px-2">
                            <v-layout row wrap>
                              <v-flex xs4>
                                <v-icon :class="convocation.statut ? 'text-success' : 'text-danger' ">{{convocation.statut?'check_circle':'cancel'}}</v-icon>
                              </v-flex>
                              <v-flex xs8 text-xs-left>
                                <h4 class="font-weight-bold">Statut</h4>
                                <span class="subheading" v-if="convocation.statut">Valide</span><span class="subheading" v-else>Annulée</span>
                              </v-flex>
                            </v-layout>
                          </div>
                        </v-flex>
                        <v-flex xs12>
                          <div class="info-item elevation-3 py-4 px-2">
                            <v-layout row wrap>
                              <v-flex xs4>
                                <v-icon>calendar_today</v-icon>
                              </v-flex>
                              <v-flex xs8 text-xs-left>
                                <h4 class="font-weight-bold">Créée le</h4>
                                <span class="subheading">{{convocation.created_at && GetFormattedDate(convocation.created_at)}}</span>
                              </v-flex>
                            </v-layout>
                          </div>
                        </v-flex>
                      </v-flex>
                    </v-layout>
                    <v-divider class="my-5" v-if="index + 1 < candidat.concourtime.length"></v-divider>
                  </v-container>
                </div>
                <v-layout row wrap text-xs-center justify-center align-center style="height:50vh" v-else>
                  <span class="display-2">Etape non renseignée...</span>
                </v-layout>
                <!-- <div class="row pt-4" v-if="candidat.convocation && candidat.convocation.length">
                                    <div class="col-md table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th>ID convocation</th>
                                                <th>Réf convocation</th>
                                                <th>Lien</th>
                                                <th>Statut</th>
                                                <th>Créée le</th>
                                            </thead>
                                            <tbody>
                                                <tr v-for="convocation in candidat.convocation">
                                                    <td>{{ convocation.id}}</td>
                                                    <td>{{ convocation.ref}}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center"><i
                                                                class="material-icons mr-2 text-muted">visibility</i><a
                                                                :href="'convocation?api_token='+api_token+'&id='+convocation.id">
                                                    </td>
                                                    <td><span v-if="convocation.statut"
                                                            class="text-success d-flex align-items-center"><i
                                                                class="material-icons mr-2">check_circle</i>Valide</span><span
                                                            class="text-danger d-flex align-items-center" v-else><i
                                                                class="material-icons mr-2">cancel</i> Annulée</span>
                                                    </td>
                                                    <td>{{convocation.created_at}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row pt-4" v-else>
                                    <div class="col-md-6">Etape non renseignée...</div>
                                </div> -->
              </v-tab-item>
              <v-tab-item class="p-3" value="tab-Statistiques">
                <v-container grid-list-lg>
                  <v-layout row wrap text-xs-center class="align-items-stretch" v-if="candidat.stat">
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>alarm</v-icon>
                        <h4 class="font-weight-bold">Dernière connexion</h4>
                        <span class="subheading">{{ candidat.stat.last_login}}</span>
                      </div>
                    </v-flex>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>settings_remote</v-icon>
                        <h4 class="font-weight-bold">IP de connexion</h4>
                        <span class="subheading">{{ candidat.stat.last_ip}}</span>
                      </div>
                    </v-flex>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>how_to_reg</v-icon>
                        <h4 class="font-weight-bold">Niveau du lead</h4>
                        <span class="subheading">{{ candidat.stat.lead_level}}/4</span>
                      </div>
                    </v-flex>
                    <v-flex md3>
                      <div class="info-item elevation-3 py-4 px-2">
                        <v-icon>show_chart</v-icon>
                        <h4 class="font-weight-bold">Nb Total connexions</h4>
                        <span class="subheading">{{ candidat.stat.total_connexions}}</span>
                      </div>
                    </v-flex>
                  </v-layout>
                  <v-layout row wrap text-xs-center justify-center align-center style="height:50vh" v-else>
                    <span class="display-2">Etape non renseignée...</span>
                  </v-layout>
                </v-container>
                <!-- <div class="col-md">
                                    <i class="material-icons mr-1 text-green">alarm</i>Dernière connexion:
                                    <b v-if="candidat.stat">{{ candidat.stat.last_login}}</b>
                                    <i class="material-icons mr-1 text-green">settings_remote</i> IP de connexion:
                                    <b v-if="candidat.stat">{{ candidat.stat.last_ip}}</b>
                                    <i class="material-icons mr-1 text-green">how_to_reg</i>Niveau du lead:
                                    <b v-if="candidat.stat">{{ candidat.stat.lead_level}}/4</b>
                                    <i class="material-icons mr-1 text-green">show_chart</i>Nb Total connexions:
                                    <b v-if="candidat.stat">{{ candidat.stat.total_connexions}}</b>
                                </div> -->
              </v-tab-item>
            </v-tabs>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import XLSX from "xlsx";

export default {
  props: {
    size: {
      type: Number,
      required: false,
      default: 10
    }
  },
  data() {
    return {
      search: "",
      dialog: false,
      mes_candidats: [],
      headers: [{
        text: "id",
        value: "id",
      }, {
        text: "Nom",
        value: "last_name",
      }, {
        text: "Prenom",
        value: "first_name",
      }, {
        text: "Entretiens",
        value: "concourtime[0].uirformation.titre",
      }, {
        text: "Convocations",
        value: "convocation.length",
      }, {
        text: "Etape",
        value: "stat.lead_level",
      }, {
        text: "Enregistré le",
        value: "created_at",
      }],
      var_candidats: [],
      excel_candidats: [],
      api_token: "",
      admin: "",
      candidat: "",
      forma_choisies:"",
      pageNumber: 0
    };
  },
  computed: {
    pageCount() {
      let l = this.mes_candidats.length,
        s = this.size;
      return Math.ceil(l / s);
    },
    paginatedData() {
      const start = this.pageNumber * this.size,
        end = start + this.size;
      return this.mes_candidats.slice(start, end);
    }
  },
  mounted: function() {
    this.api_token = localStorage.getItem("api_token_admin");
    axios.get("utilisateur_t?api_token=" + this.api_token).then(response => {
      this.admin = response.data.admin;
    }).catch(error => {
      alert(error);
    });
    axios.get("/gestion/api/candidats?api_token=" + this.api_token).then(response => {
      this.mes_candidats = this.var_candidats = response.data.map(candidat=>{
        candidat.created_at=this.GetFormattedDate(candidat.created_at)
        return candidat
      });
    }).catch(error => {
      alert(error);
    });
  },
  methods: {
    GetFormattedDate(dateString) {
      var format=function(s){return String(s)[1]?s:'0'+s; }
      var todayTime = new Date(dateString);
      var result='';

      if(dateString.includes('/')||dateString.includes('-')){
          var month = format(todayTime.getMonth() + 1);
          var day = format(todayTime.getDate());
          var year = format(todayTime.getFullYear());
          result+=day + "/" +month + "/" + year
      }

      if (dateString.includes(':')){
          var H = format(todayTime.getHours());
          var M = format(todayTime.getMinutes());
          var S = format(todayTime.getSeconds());
          result+=' '+H+':'+M+':'+S;
      }

      return result;
    }
    ,
    toxlsx: function() {
      var ws = XLSX.utils.json_to_sheet(this.mes_candidats.map(candidat => ({
        id: candidat.id,
        first_name: candidat.first_name,
        last_name: candidat.last_name,
        email: candidat.email,
        gender: candidat.profil ? candidat.profil.gender ? "Homme" : "Femme" : "",
        cin: candidat.profil ? candidat.profil.cin : "",
        country_id: candidat.profil ? candidat.profil.country_id : "",
        city_id: candidat.profil ? candidat.profil.city_id : "",
        address: candidat.profil ? candidat.profil.address : "",
        phone: candidat.profil ? candidat.profil.phone : "",
        created_at: candidat.created_at
      })));
      /* add to workbook */
      var wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, "Candidats");
      /* generate an XLSX file */
      XLSX.writeFile(wb, "candidats.xlsx");
    },
    fiche_candidat: function(candidat_id) {
      axios.get("utilisateur?api_token=" + this.api_token + "&id=" + candidat_id).then(response => {
        this.candidat = response.data.candidat;

        this.forma_choisies = response.data.formations_choisies;

      }).catch(error => {
        alert(error);
      });
      $("#fiche").modal("show");
    },
    nextPage() {
      this.pageNumber++;
    },
    prevPage() {
      this.pageNumber--;
    }
  }
};

</script>
<style>

.nav-link {
  padding: 0.1rem 1rem !important;
  color: #1c96ad;
}

.nav-link.active {
  background: #1c96ad !important;
}

#nav-tab {
  border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
  padding-bottom: 15px;
}

a.v-tabs__item:hover {
  color: inherit;
  text-decoration: none;
}

@media (min-width: 1000px) {
  .fiche-modal.modal-dialog {
    max-width: 1000px;
    /* margin: 1.75rem auto; */
  }
}

.info-item {
  height: 100%;
  transition: all .4s;
}

.info-item:hover {
  transform: translateY(-8px);
  box-shadow: 0 3px 5px -1px rgba(0, 0, 0, .2), 0 5px 8px 0 rgba(0, 0, 0, .14), 0 3px 16px 0 rgba(0, 0, 0, .2) !important;
}

.info-item:hover .v-icon {
    color: rgb(28, 150, 173);
  }

.info-item .v-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}
.profile-info {
  background: rgba(150, 150, 150, 0.1);
  border-radius: 10px;
}
.profile-info .v-icon__profile {
  color: rgb(100, 100, 100);
  font-size: 9rem;
}

</style>
