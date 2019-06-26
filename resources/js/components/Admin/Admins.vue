<template>
  <div>
    <h2 class="text-uppercase font-weight-light mb-4 text-green">Administrateurs</h2>

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

      <v-data-table :headers="headers" :items="mes_admins" :search="search">
        <template v-slot:items="props">
          <td>{{ props.item.id }}</td>
          <td>{{ props.item.last_name }}</td>
          <td>{{ props.item.first_name }}</td>
          <td>{{ props.item.email }}</td>
          <td>
            <v-img
              :src="'/images/'+props.item.uirecole.logo_path"
              style="width:100px"
              v-if="props.item.uirecole"
            ></v-img>
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
              <div class="input-group">
                <input
                  type="text"
                  name="first_name"
                  placeholder="Prénom..."
                  class="form-control"
                  v-model="first_name"
                >
                <input
                  type="text"
                  name="last_name"
                  placeholder="Nom..."
                  class="form-control"
                  v-model="last_name"
                >
              </div>

              <label for="email" class="mt-2">E-mail</label>
              <input type="text" name="email" class="form-control" v-model="email">

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
              <div class="input-group">
                <input
                  type="text"
                  name="first_name"
                  placeholder="Prénom..."
                  class="form-control"
                  v-model="first_name"
                >
                <input
                  type="text"
                  name="last_name"
                  placeholder="Nom..."
                  class="form-control"
                  v-model="last_name"
                >
              </div>

              <label for="email" class="mt-2">E-mail</label>
              <input type="text" name="email" class="form-control" v-model="email">

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
        { text: "Nom", value: "last_name" },
        { text: "Prenom", value: "first_name" },
        { text: "Email", value: "email" },
        { text: "Ecole" },
        { text: "Actions", align: "center" }
      ],
      ecoles: [],
      mes_admins: [],
      var_admins: [],
      to_delete: "",
      id: "",
      first_name: "",
      last_name: "",
      email: "",
      uirecole_id: "1",
      api_token: ""
    };
  },

  computed: {},

  mounted: function() {
    this.api_token = localStorage.getItem("api_token_admin");

    axios
      .get("/gestion/api/utilisateurs?api_token=" + this.api_token)
      .then(response => {
        this.var_admins = this.mes_admins = response.data.admins;
        this.ecoles = response.data.ecoles;
      })
      .catch(error => {
        alert(error);
      });
  },

  methods: {
    deletethis: function(id) {
      axios
        .delete("utilisateurs", {
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
        .put("utilisateurs", {
          api_token: this.api_token,
          id: id,
          first_name: this.first_name,
          last_name: this.last_name,
          email: this.email,
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
      axios
        .post("utilisateurs", {
          api_token: this.api_token,
          first_name: this.first_name,
          last_name: this.last_name,
          email: this.email,
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
        .get("utilisateur?id=" + id + "&api_token=" + this.api_token)
        .then(response => {
          this.id = response.data.admin.id;
          this.first_name = response.data.admin.first_name;
          this.last_name = response.data.admin.last_name;
          this.email = response.data.admin.email;
          this.uirecole_id = response.data.admin.uirecole_id;
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
