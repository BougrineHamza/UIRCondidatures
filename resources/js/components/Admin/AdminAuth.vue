<template>
    <div class="row h-100 w-100">
      <div class="h-auto m-auto" style="width:320px">
          <h2 class="font-light text-green text-uppercase mb-4">Connexion</h2>
          <div class="form-group">
              <label for="email">Votre E-mail</label>
              <input type="email" name="email" class="form-control" v-model="email" placeholder="Ex: karim@uir.ac.ma" />
              <br />
              <label for="password">Mot de passe</label>
              <input type="password" class="form-control" @keypress.enter="connect()" name="password"
                  v-model="password" />
              <button class="btn btn-primary mt-3" @click.prevent="connect()">
                  <span v-if="!loading">Connexion</span>
                  <svg v-else class="spinner" width="10px" height="10px" viewBox="0 0 66 66"
                      xmlns="http://www.w3.org/2000/svg">
                      <circle class="path" fill="none" stroke-width="22" stroke-linecap="round" cx="33" cy="33" r="23">
                      </circle>
                  </svg>
              </button>

          </div>
      </div>
    </div>
</template>
<script>
    import axios from 'axios';
    export default {

        data() {
            return {
                email: '',
                password: '',
                loading: false

            }
        },

        computed: {

        },

        methods: {
            connect: function () {
                this.loading = true;
                axios.post('connexion_encours', {
                    email: this.email,
                    password: this.password
                }).then(response => {
                    this.loading = false;
                    localStorage.setItem("api_token_admin", response.data.api_token);
                    window.location.replace('/gestion/candidats?api_token=' + response.data.api_token);
                }).catch(error => {
                    alert(error);
                    this.loading = false;
                });
            }
        }

    }

</script>
