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
        <v-toolbar-title>Ecoles ({{var_ecoles.length }})</v-toolbar-title>
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

      <v-data-table :headers="headers" :items="var_ecoles" :search="search">
        <template v-slot:items="props">
          <td>{{ props.item.id }}</td>
          <td>
            <v-img :src="'/images/'+props.item.logo_path" width="100"></v-img>
          </td>
          <td>{{ props.item.titre }}</td>
          <td>{{ props.item.color }}</td>
          <td class="justify-center layout px-0">
            <v-btn @click="edit(props.item.id)" icon>
              <v-icon>edit</v-icon>
            </v-btn>
            <v-btn @click="deleteit(props.item.id)" icon>
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
                        <th>Logo</th>
                        <th>Titre</th>
                        <th>Couleur</th>
                        <th class="text-right"><a href="#" @click.prevent="addit()">+ Ajouter</a></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="ecole in var_ecoles">
                        <td>{{ ecole.id }}</td>
                        <td><v-img :src="'/images/'+ecole.logo_path" style="width:100px" /></td>
                        <td>{{ ecole.titre }}</td>
                        <td>{{ ecole.color }}</td>
                        <td>
                            <a href="#" class="btn btn-primary" @click.prevent="edit(ecole.id)"><i
                                    class="material-icons text-08">edit</i></a>
                            <a href="#" class="btn btn-secondary" @click.prevent="deleteit(ecole.id)"><i
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
              <label for="titre">Nom école</label>
              <input type="text" name="titre" class="form-control" v-model="titre">

              <label for="logo_path" class="mt-2">Sigle Ecole</label>
              <input
                type="text"
                name="logo_path"
                class="form-control text-uppercase"
                v-model="logo_path"
              >

              <label for="color" class="mt-2">Couleur</label>
              <input type="text" name="color" class="form-control" v-model="color">
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
            >Supprimer</button>
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
              <label for="titre">Nom école</label>
              <input type="text" name="titre" class="form-control" v-model="titre">

              <label for="logo_path" class="mt-2">Sigle Ecole</label>
              <input
                type="text"
                name="logo_path"
                class="form-control text-uppercase"
                v-model="logo_path"
              >

              <label for="color" class="mt-2">Couleur</label>
              <input type="text" name="color" class="form-control" v-model="color">
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
        { text: "Logo" },
        { text: "Titre", value: "titre" },
        { text: "Couleur", value: "color" },
        { text: "Actions", align: "center" }
      ],
      mes_ecoles: [],
      var_ecoles: [],
      to_delete: "",
      titre: "",
      color: "",
      logo_path: "",
      id: "",
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
      .get("/gestion/api/ecoles?api_token=" + this.api_token)
      .then(response => {
        this.var_ecoles = this.mes_ecoles = response.data.ecoles;
      })
      .catch(error => {
        alert(error);
      });
  },

  methods: {
    deletethis: function(id) {
      axios
        .delete("ecoles", {
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
      axios
        .put("ecoles", {
          api_token: this.api_token,
          id: id,
          titre: this.titre,
          color: this.color,
          logo_path: "/" + this.logo_path.toLowerCase() + ".jpg"
        })
        .then(response => {
          location.reload();
        })
        .catch(error => {
          alert(error);
        });
    },
    store: function() {
      axios
        .post("ecoles", {
          api_token: this.api_token,
          titre: this.titre,
          color: this.color,
          logo_path: "/" + this.logo_path.toLowerCase() + ".jpg"
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
        .get("ecole?id=" + id + "&api_token=" + this.api_token)
        .then(response => {
          this.id = response.data.ecole.id;
          this.titre = response.data.ecole.titre;
          this.color = response.data.ecole.color;
          this.logo_path = response.data.ecole.logo_path.substring(
            1,
            response.data.ecole.logo_path.length - 4
          );
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
    }
  }
};
</script>
