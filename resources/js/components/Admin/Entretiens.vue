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
        <v-toolbar-title>Entretiens ({{var_entretiens.length}})</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-text-field
          v-model="search"
          append-icon="search"
          label="Rechercher"
          single-line
          hide-details
        ></v-text-field>
        <v-spacer></v-spacer>

        <v-btn color="primary" dark @click="addit()">Ajouter</v-btn>
        <v-btn color="primary" dark @click="toxlsx()">Exporter en Excel</v-btn>
        <v-btn color="primary" dark class="position-relative">
          Importer
          <input
          size="60"
            type="file"
            style="position:absolute; width:100%; z-index: 9; opacity:0;"
            @change="importit()"
            id="file"
            ref="file"
          >
        </v-btn>
        <div class="position-relative">
          <v-btn
            color="primary"
            dark
            @click.stop="click('.search_by_date input')"
          >Rechercher par Date</v-btn>
          <date-pick
            :format="'DD/MM/YYYY'"
            class="form-control search_by_date"
            v-model="search"
            :weekdays="weekdays"
            :months="months"
          ></date-pick>
        </div>
      </v-toolbar>

      <v-data-table :headers="headers" :items="mes_entretiens" :search="search" disable-initial-sort>
        <template v-slot:items="props">
          <td>{{ props.item.id }}</td>
          <td>{{ props.item.concourdate.date_concours }}</td>
          <td>{{ props.item.time }}</td>
          <td>
            <b class="text-green text-uppercase">{{props.item.jury?props.item.jury.name:''}}</b>
            <br>
            ID:
            {{ props.item.jury_id }}
          </td>
          <td>
            <v-img
              v-if="props.item.uirformation&&props.item.uirformation.uirecole"
              :src="'/images/'+props.item.uirformation.uirecole.logo_path"
              style="width:90px"
            ></v-img>
          </td>
          <td>{{ props.item.uirformation?props.item.uirformation.titre:'' }}</td>
          <td>
            <span class="text-uppercase" v-if="props.item.user">
              <b class="text-green">
                {{props.item.user.first_name}}
                {{props.item.user.last_name}}
              </b>
              <br>
              ID: {{props.item.user_id}}
              <br>
              <font v-if="props.item.user.profil">CIN: {{props.item.user.profil.cin}}</font>
            </span>
          </td>
          <td class="">
            <span v-if="props.item.statut == 0"><v-chip color="red" text-color="white">
              Refusé</v-chip></span>
            <span v-else-if="props.item.statut == 1"><v-chip color="green" text-color="white">Accepté</v-chip></span>
            <span v-else-if="props.item.statut == 4"><v-chip color="grey" text-color="white">Absent</v-chip></span>
            <span v-else-if="props.item.statut == 9"><v-chip color="grey" text-color="white">Liste d'attente</v-chip></span>

          </td>
          <td class="justify-center layout px-0">

            <v-menu
            v-if="props.item.user"
      transition="slide-y-transition"
      bottom
    >
      <template v-slot:activator="{ on }">

        <v-btn fab dark small outline color="teal"
          v-on="on">
              <v-icon dark>gavel</v-icon>
        </v-btn>


      </template>
      <v-list style="width:150px">
        <v-list-tile
          v-for="(item, i) in menu"
          :key="i"
          @click="updt_before(props.item.concourdate.date_concours,item.statut,props.item.id)"
        >
          <v-list-tile-title >
            <v-icon :color="item.color">{{item.icon}}</v-icon> {{ item.title }}
          </v-list-tile-title>
        </v-list-tile>
      </v-list>
    </v-menu>
    <v-btn fab small disabled
           v-else>
              <v-icon dark>gavel</v-icon>
        </v-btn>



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
              <div v-if="admin.uirecole_id == 0">
                <label for="uirecole_id" class="mt-2">Ecole</label>
                <select
                  name="uirecole_id"
                  class="form-control"
                  @change.prevent="getformasjurys($event)"
                  v-model="key"
                >
                  <option
                    v-for="ecole in ecoles"
                    :value="ecole.id"
                    :selected="ecole.id == uirecole_id"
                  >{{ ecole.titre }}</option>
                </select>
              </div>

              <label for="uirformation_id" class="mt-2">Formation</label>
              <select
                name="uirformation_id"
                class="form-control"
                @change.prevent="changeformationid($event)"
              >
                <option
                  v-for="formation in formations_list"
                  :value="formation.id"
                  :selected="formation.id == uirformation_id"
                >{{ formation.titre }}</option>
              </select>

              <label for="jury_id" class="mt-2">Jury</label>
              <select name="jury_id" class="form-control" @change.prevent="changejuryid($event)">
                <option
                  v-for="jury in jurys_list"
                  :value="jury.id"
                  :selected="jury.id == jury_id"
                >{{ jury.name }}</option>
              </select>

              <label class="mt-2">Date/Heure entretien</label>
              <div class="input-group">
                <date-pick
                  :inputAttributes="{placeholder: 'Ex: 04-10-2019'}"
                  :displayFormat="'DD-MM-YYYY'"
                  class="form-control"
                  v-model="date_concours"
                  :weekdays="weekdays"
                  :months="months"
                ></date-pick>
                <input
                  placeholder="Ex: 08:00"
                  type="text"
                  name="time"
                  class="form-control"
                  v-model="time"
                >
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
              <div v-if="admin.uirecole_id == 0">
                <label for="uirecole_id" class="mt-2">Ecole</label>
                <select
                  name="uirecole_id"
                  class="form-control"
                  @change.prevent="getformasjurys($event)"
                  v-model="key"
                >
                  <option
                    v-for="ecole in ecoles"
                    :value="ecole.id"
                    :selected="uirecole_id == ecole.id"
                  >{{ ecole.titre }}</option>
                </select>
              </div>

              <label for="uirformation_id" class="mt-2">Formation</label>
              <select
                name="uirformation_id"
                class="form-control"
                @change.prevent="changeformationid($event)"
              >
                <option
                  v-for="formation in formations_list"
                  :value="formation.id"
                >{{ formation.titre }}</option>
              </select>

              <label for="jury_id" class="mt-2">Jury</label>
              <select name="jury_id" class="form-control" @change.prevent="changejuryid($event)">
                <option v-for="jury in jurys_list" :value="jury.id">{{ jury.name }}</option>
              </select>

              <label class="mt-2">Date/Heure entretien</label>
              <div class="input-group">
                <date-pick
                  :displayFormat="'DD-MM-YYYY'"
                  :inputAttributes="{placeholder: 'Ex: 04-10-2019'}"
                  class="form-control"
                  v-model="date_concours"
                  :weekdays="weekdays"
                  :months="months"
                ></date-pick>
                <input
                  placeholder="Ex: 08:00"
                  type="text"
                  name="time"
                  class="form-control"
                  v-model="time"
                >
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

    <!-- Modal ADD -->
    <div class="modal fade" tabindex="-1" role="dialog" id="decision">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Résultat entretien</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Un e-mail sera envoyé au candidat. Etes-vous sûr de vouloir continuer ?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Non</button>
            <button type="button" class="btn btn-primary" @click.prevent="updt()">
            <font v-if="!updt_loading">Oui</font>
            <svg class="spinner" width="10px" height="10px" viewBox="0 0 66 66"
                      xmlns="http://www.w3.org/2000/svg" v-else>
                      <circle class="path" fill="none" stroke-width="22" stroke-linecap="round" cx="33" cy="33" r="23">
                      </circle>
            </svg></v-btn>
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal ADD -->
  </div>
</template>
<style>
.vdpWithInput {
  font-size: 1em !important;
  border: 0 !important;
  padding: 0 !important;
}

.search_by_date input {
  visibility: hidden;
}
.search_by_date {
  position: absolute;
  width: 0;
  height: 0;
  top: 36px;
  left: 0;
}

.vdpComponent.vdpWithInput > input {
  width: 100%;
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
  padding-left: 10px;
  padding-right: 0 !important;
}

.vdpClearInput {
  font-size: 10px;
}

.vdpCell.selectable:hover .vdpCellContent,
.vdpCell.selected .vdpCellContent {
  background: #1c96ad;
}

.today {
  color: #1c96ad !important;
}

.vdpHeadCellContent,
.vdpCellContent {
  font-size: 1em !important;
}

.vdpOuterWrap {
  font-size: 10px !important;
}

.vdpClearInput {
  margin-top: 13px;
}

</style>
<script>
import DatePick from "vue-date-pick";
import "vue-date-pick/dist/vueDatePick.css";
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
  components: {
    DatePick
  },
  data() {
    return {
      search: "",
      updt_date: '',
      updt_val:'',
      updt_id:'',
      loading: false,
      updt_loading: false,
      file: '',
      menu: [
              {title:'Accepter', icon:'check', color:'green', statut:1},
              {title:'Refuser', icon:'clear', color:'pink', statut:0},
              {title:'Absent', icon:'rowing', color:'grey', statut:4},
              {title:'Liste d\'attente', icon:'restore', color:'grey', statut:9},
              {title:'Aucun', icon:'delete_sweep', color:'grey', statut:3},



            ],
      dialog: false,
      headers: [
        {
          text: "Id",

          value: "id"
        },
        {
          text: "Jour",

          value: "concourdate.date_concours"
        },
        {
          text: "Heure",

          value: "time"
        },
        {
          text: "Jury affecté",

          value: "jury.name"
        },
        {
          text: "Ecole"
        },
        {
          text: "Formation",
          value: "uirformation.titre"
        },
        {
          text: "Candidat affecté",
          value: "user.first_name"
        },
        {
          text: "Résultat",
          align: "center",
          value: "statut"
        },
        {
          text: "Actions",
          align: "center"
        }
      ],
      mes_entretiens: [],
      ecoles: [],
      var_entretiens: [],
      to_delete: "",
      key: "",
      uirecole_id: "",
      date_concours: "",
      time: "",
      jury_id: "",
      uirformation_id: "",
      id: "",
      formations_list: "",
      jurys_list: "",
      api_token: "",
      admin: "",
      pageNumber: 0,
      weekdays: ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"],
      months: [
        "Janvier",
        "Février",
        "Mars",
        "Avril",
        "Mai",
        "Juin",
        "Juillet",
        "Août",
        "Septembre",
        "Octobre",
        "Novembre",
        "Décembre"
      ]
    };
  },

  computed: {
    // pageCount() {
    //   let l = this.mes_entretiens.length,
    //     s = this.size;
    //   return Math.ceil(l / s);
    // },
    // paginatedData() {
    //   const start = this.pageNumber * this.size,
    //     end = start + this.size;
    //   return this.mes_entretiens.slice(start, end);
    // }
  },

  mounted: function() {
    this.api_token = localStorage.getItem("api_token_admin");

    axios
      .get("utilisateur_t?api_token=" + this.api_token)
      .then(response => {
        this.admin = response.data.admin;

        if (this.admin.uirecole_id != 0) {
          this.uirecole_id = this.admin.uirecole_id;

          axios
            .get(
              "getformasjurys?id=" +
                this.uirecole_id +
                "&api_token=" +
                this.api_token
            )
            .then(response => {

              if (response.data.formations_list.length == 0) {
                alert("Cette école n'a pas de formation");
              }

              if (response.data.jurys_list.length == 0) {
                alert("Cette école n'a pas de jury affecté");
              }

              if (
                response.data.jurys_list.length > 0 &&
                response.data.formations_list.length > 0
              ) {
                this.formations_list = response.data.formations_list;
                this.jurys_list = response.data.jurys_list;
                this.jury_id = response.data.jurys_list[0].id;
                this.uirformation_id = response.data.formations_list[0].id;
                this.uirecole_id = event.target.value;
              }
            })
            .catch(error => {
              alert(error);
            });
        }
      })
      .catch(error => {
        alert(error);
      });

    axios
      .get("/gestion/api/entretiens?api_token=" + this.api_token)
      .then(response => {
        this.var_entretiens = this.mes_entretiens = response.data.entretiens.map(entretien=>{
          entretien.concourdate.date_concours=this.GetFormattedDate(entretien.concourdate.date_concours);
          return entretien;
        });
        this.ecoles = response.data.ecoles;
      })
      .catch(error => {
        alert(error);
      });
  },
  methods: {
    GetFormattedDate: function(dateString) {
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
    importit: function(){
      this.file = this.$refs.file.files[0];

      let formData = new FormData();

      formData.append('file', this.file);

      axios.post( "/gestion/api/import?api_token=" + this.api_token,
        formData,
        {
          headers: {
              'Content-Type': 'multipart/form-data'
          }
        }
      ).then(response => {

        console.log(response);
      })
      .catch(error => {
      });

    },
    updt_before: function(date,val,id){
      this.updt_date = date;
      this.updt_val = val;
      this.updt_id = id;
      $("#decision").modal("show");

    },
    updt:function(){
      this.updt_loading = true;

      var reformatted_entretien_date = this.updt_date.slice(3,5)+'/'+this.updt_date.slice(0,2)+'/'+this.updt_date.slice(6,10);

      var entretien_date = new Date(reformatted_entretien_date);

      var today_date = new Date();

      if(entretien_date <= today_date){
          axios.put("/gestion/api/entretien",{api_token:this.api_token,statut:this.updt_val,id_entretien:this.updt_id}).then(response => {
            location.reload();
          }).catch(error => {
                alert(error);
          });
      } else {
      this.updt_loading = false;

        alert('La date de l\'entretien n\'est pas encore arrivée pour enregistrer un résultat.');
      }
    },
    toxlsx: function() {
      var ws = XLSX.utils.json_to_sheet(
        this.mes_entretiens.filter(entretien => entretien.user).map(entretien => ({
          "Formation": entretien.uirformation ? entretien.uirformation.titre : "",
          "Code Candidat": entretien.user ? entretien.user.id : "",
          "CIN Candidat":  entretien.user ? entretien.user.profil ? entretien.user.profil.cin : "" : "",
          "Civilité": entretien.user ? entretien.user.profil ? entretien.user.profil.gender ? "M" : "F" : "" : "",
          "Prénom": entretien.user ? entretien.user.first_name : "",
          "Nom": entretien.user ? entretien.user.last_name : "",
          "Université de provenance": entretien.user ? entretien.user.cursus ? entretien.user.cursus.school : "" : "",
          "Interne": entretien.user ? entretien.user.uir_student ? "Oui" : "Non"  : "",
          "E-mail": entretien.user ? entretien.user.email : "",
          "Telephone": entretien.user ? entretien.user.profil ? entretien.user.profil.phone : "" : "",
          "Date Entretien": entretien.concourdate.date_concours,
          "Heure de passage": entretien.time,
          "Nom jury": entretien.jury ? entretien.jury.name : "",
          "Décision": entretien.statut == 9 ? "Liste d\'attente" : entretien.statut == 0 ? "Refusé" : entretien.statut == 1 ? "Accepté" : entretien.statut == 4 ? "Absent" : ""

        }))
      );
      /* add to workbook */
      var wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, "Entretiens");

      /* generate an XLSX file */
      XLSX.writeFile(wb, "Entretiens.xlsx");
    },
    click(el) {
      document.querySelector(el).click();
    },
    getformasjurys: function(event) {
      this.uirecole_id = event.target.value;
      axios
        .get(
          "getformasjurys?id=" +
            this.uirecole_id +
            "&api_token=" +
            this.api_token
        )
        .then(response => {
          if (response.data.formations_list.length == 0) {
            alert("Cette école n'a pas de formation");
          }

          if (response.data.jurys_list.length == 0) {
            alert("Cette école n'a pas de jury affecté");
          }

          if (
            response.data.jurys_list.length > 0 &&
            response.data.formations_list.length > 0
          ) {
            this.formations_list = response.data.formations_list;
            this.jurys_list = response.data.jurys_list;
            this.jury_id = response.data.jurys_list[0].id;
            this.uirformation_id = response.data.formations_list[0].id;
            this.uirecole_id = event.target.value;
          }
        })
        .catch(error => {
          alert(error);
        });
    },

    deletethis: function(id) {
      axios
        .delete("entretiens", {
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
        .put("entretiens", {
          api_token: this.api_token,
          id: id,
          jury_id: this.jury_id,
          uir_formation_id: this.uirformation_id,
          time: this.time,
          date_concours: this.date_concours
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
        .post("entretiens", {
          api_token: this.api_token,
          jury_id: this.jury_id,
          uir_formation_id: this.uirformation_id,
          time: this.time,
          date_concours: this.date_concours
        })
        .then(response => {
          location.reload();
        })
        .catch(error => {
          alert(error);
        });
    },

    nextPage() {
      this.pageNumber++;
    },
    prevPage() {
      this.pageNumber--;
    },
    edit: function(id) {
      this.id = id;
      axios
        .get("entretien?id=" + id + "&api_token=" + this.api_token)
        .then(response => {
          this.jury_id = response.data.entretien.jury_id;
          this.formation_id = response.data.entretien.uir_formation_id;
          this.time = response.data.entretien.time;
          this.date_concours = response.data.date_concours;
          this.uirecole_id = response.data.uirecole_id;
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
    changeformationid: function(event) {
      this.uirformation_id = event.target.value;
    },
    changejuryid: function(event) {
      this.jury_id = event.target.value;
    }
  }
};
</script>
