<template>
  <div>
    <h2 class="text-uppercase font-weight-light mb-4 text-green">Villes</h2>

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
        <v-btn color="primary" dark class="mb-2" @click="addcountry()">Ajouter Pays</v-btn>
        <v-btn color="primary" dark class="mb-2" @click="addcity()">Ajouter Ville</v-btn>
      </v-toolbar>

      <v-data-table :headers="headers" :items="mes_villes" :search="search">
        <template v-slot:items="props">
          <td>{{ props.item.country.titre }}</td>
          <td>{{ props.item.titre }}</td>
          <td>{{ props.item.country.nationalite }}</td>
          <td class="justify-center layout px-0">
            <v-btn color="success" @click="editcountry(props.item.country.id)">
              <v-icon class="mr-2">edit</v-icon>
              <span>Pays</span>
            </v-btn>
            <v-btn color="success" @click="editcity(props.item.id)">
              <v-icon class="mr-2">edit</v-icon>
              <span>Ville</span>
            </v-btn>
            <v-btn @click="deletecity(props.item.id)" icon>
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

    <!-- Modal EDIT Country -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editcountry">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modifier Pays</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Titre Pays</label>
              <input type="text" class="form-control" v-model="country">

              <label class="mt-3">Nationalité</label>
              <input type="text" class="form-control" v-model="nationalite">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            <button
              type="button"
              class="btn btn-primary"
              @click.prevent="updatecountry(country_id)"
            >Enregistrer</button>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal Edit -->

    <!-- Modal EDIT City -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editcity">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modifier Ville</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="modal-body">
              <div class="form-group">
                <label class>Titre ville</label>
                <input type="text" class="form-control" v-model="city">
                <label class="mt-3">Choisir Pays</label>
                <select class="form-control" @change.prevent="changepays($event)">
                  <option
                    v-for="pays in var_pays"
                    :value="pays.id"
                    :selected="country_id === pays.id"
                  >{{pays.titre}}</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            <button
              type="button"
              class="btn btn-primary"
              @click.prevent="updatecity(city_id)"
            >Enregistrer</button>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal Edit -->

    <!-- Modal DELETE city-->
    <div class="modal fade" tabindex="-1" role="dialog" id="deletecity">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Supprimer Ville</h5>
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
              @click.prevent="destroycity(to_delete)"
            >Enregistrer</button>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal Delete -->

    <!-- Modal ADD country-->
    <div class="modal fade" tabindex="-1" role="dialog" id="addcountry">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Ajouter Pays</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="modal-body">
              <div class="form-group">
                <label>Titre Pays</label>
                <input type="text" class="form-control" v-model="country">

                <label class="mt-4">Nationalité</label>
                <input type="text" class="form-control" v-model="nationalite">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            <button
              type="button"
              class="btn btn-primary"
              @click.prevent="storecountry()"
            >Enregistrer</button>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal ADD -->

    <!-- Modal ADD city-->
    <div class="modal fade" tabindex="-1" role="dialog" id="addcity">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Ajouter Ville</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="modal-body">
              <div class="form-group">
                <label class>Titre ville</label>
                <input type="text" class="form-control" v-model="city">
                <label class="mt-3">Choisir Pays</label>
                <select class="form-control" @change.prevent="changepays($event)">
                  <option
                    v-for="pays in var_pays"
                    :value="pays.id"
                    :selected="country_id == pays.id"
                  >{{pays.titre}}</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-primary" @click.prevent="storecity()">Enregistrer</button>
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
      headers: [
        { text: "Pays", value: "country.titre" },
        { text: "Ville", value: "titre" },
        { text: "Nationalité", value: "country.nationalite" },
        { text: "Actions", align: "center" }
      ],
      mes_villes: [],
      mes_pays: [],
      var_villes: [],
      var_pays: [],
      to_delete: "",
      api_token: "",
      country: "",
      city: "",
      country_id: 1,
      city_id: "",
      nationalite: "",
      pageNumber: 0
    };
  },

  computed: {
    pageCount() {
      let l = this.mes_villes.length,
        s = this.size;
      return Math.ceil(l / s);
    },
    paginatedData() {
      const start = this.pageNumber * this.size,
        end = start + this.size;
      return this.mes_villes.slice(start, end);
    }
  },

  mounted: function() {
    this.var_villes = this.mes_villes;
    this.var_pays = this.mes_pays;
    this.api_token = localStorage.getItem("api_token_admin");

    axios
      .get("/gestion/api/villes?api_token=" + this.api_token)
      .then(response => {
        this.mes_villes = response.data.villes;
        this.mes_pays = response.data.payss;
      })
      .catch(error => {
        alert(error);
      });
  },

  methods: {
    nextPage() {
      this.pageNumber++;
    },
    prevPage() {
      this.pageNumber--;
    },
    updatecountry: function(id) {
      axios
        .put("pays", {
          id: id,
          titre: this.country,
          nationalite: this.nationalite,
          api_token: this.api_token
        })
        .then(response => {
          location.reload();
        })
        .catch(error => {
          alert(error);
        });
    },
    editcountry: function(id) {
      this.country_id = id;

      axios
        .get("pays_u?id=" + id + "&api_token=" + this.api_token)
        .then(response => {
          this.country = response.data.Country.titre;
          this.nationalite = response.data.Country.nationalite;
        })
        .catch(error => {
          alert(error);
        });

      $("#editcountry").modal("show");
    },
    storecountry: function() {
      axios
        .post("pays", {
          titre: this.country,
          nationalite: this.nationalite,
          api_token: this.api_token
        })
        .then(response => {
          location.reload();
        })
        .catch(error => {
          alert(error);
        });
    },
    storecity: function() {
      axios
        .post("villes", {
          titre: this.city,
          country_id: this.country_id,
          api_token: this.api_token
        })
        .then(response => {
          location.reload();
        })
        .catch(error => {
          alert(error);
        });
    },
    updatecity: function(id) {
      axios
        .put("villes", {
          id: id,
          titre: this.city,
          country_id: this.country_id,
          api_token: this.api_token
        })
        .then(response => {
          location.reload();
        })
        .catch(error => {
          alert(error);
        });
    },
    addcountry: function() {
      $("#addcountry").modal("show");
    },
    editcity: function(id) {
      this.city_id = id;

      axios
        .get("ville_u?id=" + id + "&api_token=" + this.api_token)
        .then(response => {
          this.city = response.data.ville.titre;
          this.country_id = response.data.ville.country_id;

          $("#editcity").modal("show");
        })
        .catch(error => {
          alert(error);
        });
    },
    addcity: function() {
      $("#addcity").modal("show");
    },
    destroycity: function(id) {
      axios
        .delete("villes", {
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
    deletecity: function(id) {
      this.to_delete = id;
      $("#deletecity").modal("show");
    },
    changepays: function(event) {
      this.country_id = event.target.value;
    }
  }
};
</script>
