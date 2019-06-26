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
        <v-toolbar-title>Formations ({{var_formations.length }})</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-text-field
          v-model="search"
          append-icon="search"
          label="Rechercher"
          single-line
          hide-details
        ></v-text-field>
        <v-spacer></v-spacer>
        <v-btn color="primary" dark class="mb-2" @click="addit()">Ajouter</v-btn>
      </v-toolbar>

      <v-data-table :headers="headers" :items="mes_formations" :search="search" disable-initial-sort>
        <template v-slot:items="props">
          <td>{{ props.item.id }}</td>
          <td>{{ props.item.titre }}</td>
          <td>{{ props.item.prerequis_html }}</td>
          <td>Bac+{{ props.item.condition_niveau }}</td>
          <td>
            <v-img :src="'/images/'+props.item.uirecole.logo_path" style="width:100px"></v-img>
          </td>
          <td class="justify-center layout px-0">
            <v-btn @click="edit(props.item.id)" icon>
              <v-icon>edit</v-icon>
            </v-btn>
            <v-btn @click="deleteit(props.item.id)" icon v-if="admin.role">
              <v-icon>clear</v-icon>
            </v-btn>
          </td>
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

    <!-- <div class="table-responsive">
            <table class="table table-bordered col table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Id</th>
                        <th>Titre formation</th>
                        <th>Prérequis</th>
                        <th>Niveau Min.</th>
                        <th>Ecole</th>
                        <th class="text-right"><a href="#" @click.prevent="addit()">+ Ajouter</a></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="formation in var_formations">
                        <td>{{ formation.id }}</td>
                        <td>{{ formation.titre }}</td>
                        <td>{{ formation.prerequis_html }}</td>
                        <td>Bac+{{ formation.condition_niveau }}</td>
                        <td>
                            <v-img :src="'/images/'+formation.uirecole.logo_path" style="width:100px"></v-img>
                        </td>
                        <td>
                            <a href="#" class="btn btn-primary" @click.prevent="edit(formation.id)"><i
                                    class="material-icons text-08">edit</i></a>
                            <a href="#" class="btn btn-secondary " @click.prevent="deleteit(formation.id)"><i
                                    class="material-icons text-08">clear</i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
    </div>-->
    <!-- Modal EDIT -->
    <div class="modal fade" tabindex="-1" role="dialog" id="edit">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modifier</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="titre">Titre Formation</label>
              <input type="text" name="titre" class="form-control" v-model="titre">

              <label for="prerequis_html" class="mt-2">Prérequis</label>
              <textarea
                name="prerequis_html"
                class="form-control"
                rows="3"
                v-model="prerequis_html"
              ></textarea>

              <label for="condition_niveau" class="mt-2">Niveau Min.</label>
              <div class="input-group pl-0 col-md-4">
                <div class="input-group-prepend" style="border-right:1px solid rgba(0,0,0,0.1);">
                  <span class="input-group-text" id="basic-addon1">Bac+</span>
                </div>

                <input
                  type="text"
                  name="condition_niveau"
                  class="form-control"
                  v-model="condition_niveau"
                  aria-describedby="basic-addon1"
                >
              </div>

              <div v-if="admin.uirecole_id == 0">
                <label for="ecoleuir_id" class="mt-2">Ecole</label>
                <select
                  name="ecoleuir_id"
                  class="form-control"
                  @change.prevent="changeecoleid($event)"
                >
                  <option
                    v-for="ecole in ecoles"
                    :value="ecole.id"
                    :selected="ecole.id == uirecole_id"
                  >{{ ecole.titre }}</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            <button
              type="button"
              class="btn btn-primary"
              @click.prevent="updatethis(id)"
            >Enregistrer</button>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal Edit -->

    <!-- Modal DELETE -->
    <div class="modal fade" tabindex="-1" role="dialog" id="delete">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Supprimer</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>
              Etes-vous sûr de vouloir supprimer
              <font class="text-danger">ID {{ to_delete }}</font> ?
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            <button
              type="button"
              class="btn btn-primary"
              @click.prevent="deletethis(to_delete)"
            >Enregistrer</button>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal Delete -->

    <!-- Modal ADD -->
    <div class="modal fade" tabindex="-1" role="dialog" id="add">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Ajouter</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="titre">Titre Formation</label>
              <input type="text" name="titre" class="form-control" v-model="titre">

              <label for="prerequis_html" class="mt-2">Prérequis</label>
              <textarea
                name="prerequis_html"
                class="form-control"
                rows="3"
                v-model="prerequis_html"
              ></textarea>

              <label for="condition_niveau" class="mt-2">Niveau Min.</label>
              <div class="input-group pl-0 col-md-4">
                <div class="input-group-prepend" style="border-right:1px solid rgba(0,0,0,0.1);">
                  <span class="input-group-text" id="basic-addon2">Bac+</span>
                </div>

                <input
                  type="text"
                  name="condition_niveau"
                  class="form-control"
                  v-model="condition_niveau"
                  aria-describedby="basic-addon2"
                >
              </div>

              <div v-if="admin.uirecole_id == 0">
                <label for="ecoleuir_id" class="mt-2">Ecole</label>
                <select
                  name="ecoleuir_id"
                  class="form-control"
                  @change.prevent="changeecoleid($event)"
                >
                  <option v-for="ecole in ecoles" :value="ecole.id">{{ ecole.titre }}</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-primary" @click.prevent="store()">Enregistrer</button>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal ADD -->
  </div>
</template>
<script>
import axios from "axios";
export default {
  data() {
    return {
      search: "",
      dialog: false,
      headers: [
        { text: "Id", value: "id" },
        { text: "Titre formation", value: "titre" },
        { text: "Prérequis", value: "prerequis_html" },
        { text: "Niveau Min.", value: "condition_niveau" },
        { text: "Logo" },
        { text: "Actions", align: "center" }
      ],
      mes_formations: [],
      ecoles: [],
      var_formations: [],
      to_delete: "",
      id: "",
      titre: "",
      prerequis_html: "",
      condition_niveau: "",
      uirecole_id: 1,
      api_token: "",
      admin: ""
    };
  },

  computed: {},

  mounted: function() {
    this.api_token = localStorage.getItem("api_token_admin");
    axios
      .get("utilisateur_t?api_token=" + this.api_token)
      .then(response => {
        this.admin = response.data.admin;
      })
      .catch(error => {
        alert(error);
      });
    axios
      .get("/gestion/api/formations?api_token=" + this.api_token)
      .then(response => {
        this.ecoles = response.data.ecoles;
        this.var_formations = this.mes_formations = response.data.formations;
      })
      .catch(error => {
        alert(error);
      });
  },

  methods: {
    deletethis: function(id) {
      axios
        .delete("formations", {
          data: {
            id: id,
            api_token: this.api_token
          }
        })
        .then(response => {
          location.reload();
        })
        .catch(error => {
          alert(error);
        });
    },
    updatethis: function(id) {
      if (this.admin.uirecole_id != 0) {
        this.uirecole_id = this.admin.uirecole_id;
      }

      axios
        .put("formations", {
          api_token: this.api_token,
          id: id,
          titre: this.titre,
          prerequis_html: this.prerequis_html,
          condition_niveau: this.condition_niveau,
          uirecole_id: this.uirecole_id
        })
        .then(response => {
          location.reload();
        })
        .catch(error => {
          alert(error);
        });
    },
    store: function() {
      if (this.admin.uirecole_id != 0) {
        this.uirecole_id = this.admin.uirecole_id;
      }

      axios
        .post("formations", {
          api_token: this.api_token,
          titre: this.titre,
          prerequis_html: this.prerequis_html,
          condition_niveau: this.condition_niveau,
          uirecole_id: this.uirecole_id
        })
        .then(response => {
          location.reload();
        })
        .catch(error => {
          alert(error);
        });
    },
    edit: function(id) {
      axios
        .get("formation?id=" + id + "&api_token=" + this.api_token)
        .then(response => {
          this.id = response.data.formation.id;
          this.titre = response.data.formation.titre;
          this.prerequis_html = response.data.formation.prerequis_html;
          this.condition_niveau = response.data.formation.condition_niveau;
          this.uirecole_id = response.data.formation.uirecole_id;
        })
        .catch(error => {
          alert(error);
        });

      $("#edit").modal("show");
    },
    deleteit: function(id) {
      this.to_delete = id;
      $("#delete").modal("show");
    },
    addit: function() {
      $("#add").modal("show");
    },
    changeecoleid: function(event) {
      this.uirecole_id = event.target.value;
    }
  }
};
</script>
