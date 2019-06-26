<template>
  <div>
    <h2 class="text-uppercase font-weight-light mb-4 text-green">Spécialités</h2>

    <section class="gp_table elevation-3 px-2 py-3" style="background: white !important;">
      <v-toolbar flat color="white">
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

      <v-data-table :headers="headers" :items="mes_specialites" :search="search">
        <template v-slot:items="props">
          <td>{{ props.item.id }}</td>
          <td>{{ props.item.titre }}</td>
          <td>
            <span v-if="props.item.uirformation_id_map">UIR</span>
            <span v-else>Externe</span>
          </td>
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
                        <th>Titre</th>
                        <th>Type</th>
                        <th class="text-right"><a href="#" @click.prevent="addit()">+ Ajouter</a></th>

                    </tr>
                </thead>
                <tbody>
                    <tr v-for="specialite in var_specialites">
                        <td>{{ specialite.id }}</td>
                        <td>{{ specialite.titre }}</td>
                        <td><span v-if="specialite.uirformation_id_map">UIR</span><span v-else>Externe</span></td>

                        <td>
                            <a href="#" class="btn btn-primary" @click.prevent="edit(specialite.id)"><i
                                    class="material-icons text-08">edit</i></a>
                            <a href="#" class="btn btn-secondary " @click.prevent="deleteit(specialite.id)"><i
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
              <label for="titre">Titre spécialité</label>
              <input type="text" name="titre" class="form-control" v-model="titre">
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
              <label for="titre">Titre spécialité</label>
              <input type="text" name="titre" class="form-control" v-model="titre">
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
        { text: "Titre", value: "titre" },
        { text: "Type", value: "uirformation_id_map" },
        { text: "Actions", align: "center" }
      ],
      mes_specialites: [],
      var_specialites: [],
      to_delete: "",
      titre: "",
      id: "",
      api_token: ""
    };
  },

  computed: {},

  mounted: function() {
    this.api_token = localStorage.getItem("api_token_admin");
    this.var_specialites = this.mes_specialites;

    axios
      .get("/gestion/api/specialites?api_token=" + this.api_token)
      .then(response => {
        this.mes_specialites = this.var_specialites = response.data.specialites;
      })
      .catch(error => {
        alert(error);
      });
  },

  methods: {
    deletethis: function(id) {
      axios
        .delete("specialites", {
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
        .put("specialites", {
          api_token: this.api_token,
          id: id,
          titre: this.titre
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
        .post("specialites", {
          api_token: this.api_token,
          titre: this.titre
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
        .get("specialite?id=" + id + "&api_token=" + this.api_token)
        .then(response => {
          this.id = response.data.specialite.id;
          this.titre = response.data.specialite.titre;
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
