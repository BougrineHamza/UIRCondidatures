<template>
    <div class="mt-5 text-center" >
        <h4 class="grand-titre2 text-uppercase text-center">Paramètres</h4><span>Modifier vos paramètres de compte</span>
        <span class="d-none">{{store_email}}</span>
        <div style="width:320px; margin:auto;">
            <div class="my-5">
                <div>
                    <div class="mb-2">
                        <h6 class="text-uppercase text-left">Modifier mon e-mail</h6>
                        <div class="row form">
                            <div class="col text-left"><label class="col-form-label" for="email">Nouvelle adresse e-mail</label></div>
                        </div>
                        <div class="position-relative">
                            <input name="email" type="email" v-model="newemail" class="form-control" v-validate="'required|email'" :class="{'haserror': errors.has('form-10.email') }" data-vv-as="E-mail" data-vv-scope="form-10">
                            <small v-show="errors.has('form-10.email')" class="help">{{ errors.first('form-10.email') }}</small>
                        </div>
                    </div>
                    <div>
                        <div class="text-right mb-4">
                            <button class="btn btn-primary" type="button" @click="changeemail('form-10')">Modifier e-mail</button>
                        </div>
                    </div>
                    <div class="mb-2 pt-4">
                        <h6 class="text-uppercase text-left">Modifier mon mot de passe</h6>
                        <div class="row form">
                            <div class="col text-left"><label class="col-form-label" for="password">Nouveau Mot de passe</label></div>
                        </div>
                        <div class="position-relative password">
                            <label class="show-pass" for="passw">
                            <input type="checkbox" id="passw" name="passw" v-model="show_pass" @change="showmypassword(show_pass)" />
                            <i class="material-icons"></i>
                        </label>
                            <input name="password" :type="show_password" class="form-control" v-validate="'required|min:4'" :class="{'haserror': errors.has('form-11.password') }" v-model="password" ref="password" data-vv-as="Mot de passe" data-vv-scope="form-11">
                            <small v-show="errors.has('form-11.password')" class="help">{{ errors.first('form-11.password') }}</small>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="row form">
                            <div class="col text-left"><label class="col-form-label" for="password">Confirmer Mot de passe</label></div>
                        </div>
                        <div class="position-relative">
                            <input name="confirm_password" :type="show_password" class="form-control"   v-validate="'required|confirmed:password'"  :class="{'haserror': errors.has('form-11.confirm_password') }" data-vv-as="Confirmation Mot de passe" data-vv-scope="form-11" />
                            <small v-show="errors.has('form-11.confirm_password')" class="help">{{ errors.first('form-11.confirm_password') }}</small>
                        </div>
                    </div>
                    </div>
                    <div>
                        <div class="text-right">
                            <button class="btn btn-primary" type="button" @click="changepass('form-11')">Modifier mot de passe</button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</template>
<script>
    // register the plugin on vue


export default {
    props: [],

    data() {
        return {
            newemail: '',
            password:'',
            show_password: 'password',
            show_pass: false

        }
    },

    mounted: function() {
    },
    computed: {
        store_email() {
            this.newemail = this.$store.state.user.email;
            return this.$store.state.user.email;
        }
    },

    methods: {
        changeemail: function(scope) {
            this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.$store.dispatch('tellChangeEmail', {newemail: this.newemail, toasted: this.$toasted});

                    }
                });
            },
        showmypassword: function(bool) {
            if (bool) {
                this.show_password = 'text';
            } else {
                this.show_password = 'password';
            }
        },
        changepass: function(scope){
                 this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.$store.dispatch('tellChangePassword', {newpass: this.password, toasted: this.$toasted});
                    }
                });
            },
            }

    }

</script>
