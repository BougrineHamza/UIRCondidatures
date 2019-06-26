<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" href="/images/favicon.png" sizes="16x16" type="image/png">
    <title>UIR</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Roboto:100,300,400,500,700,900|Open+Sans:300,400,600,700">
    <link rel="stylesheet" href="/dist/app.css" />
    <link rel="stylesheet" href="/dist/vuetify.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <style>
        a.v-list__tile {
            color: rgba(0, 0, 0, 0.7);
        }

        a.v-list__tile:hover,
        a.v-btn--icon:hover {
            text-decoration: none;
            color: rgba(15, 15, 15, 0.9) !important;
        }

        a.v-list__tile--active {
            color: rgba(0, 0, 0, 0.9) !important;
            background: rgba(150, 150, 150, 0.2);
        }
        .v-toolbar {
          box-shadow: none !important;
        }
        
        .v-toolbar__content{
            padding:0 5px;
        }

        .v-toolbar input[type=text],
        .v-toolbar input[type=password],
        .v-toolbar input[type=email] {
            height: 50px !important;
            background: none !important;
        }

    </style>
</head>

<body>

    <v-app id="app" class="v-app" style="min-height:100vh;visibility: hidden">
        <v-toolbar style="background:#1c96ad;" dark app :clipped-left="$vuetify.breakpoint.mdAndUp">
            <v-toolbar-title style="width: 300px;" class="ml-0">
                <v-toolbar-side-icon v-if="$route.name!='admin-login'" @click.stop="drawer = !drawer">
                </v-toolbar-side-icon>
                <img src="/images/uir-logo_white.png" style="width:140px;" />
            </v-toolbar-title>
            <v-spacer></v-spacer>
            @if(isset($_GET['api_token']))
            <v-tooltip bottom v-if="$route.name!='admin-login'">
                <template v-slot:activator="{ on }">
                    <v-btn v-on="on" @click.stop="deconnexion('@php echo $_GET['api_token']; @endphp')" icon>
                        <v-icon>power_settings_new</v-icon>
                    </v-btn>
                </template>
                <span class="subheading">Deconnexion</span>
            </v-tooltip>
            @endif
        </v-toolbar>

        @if(isset($_GET['api_token']))
        <v-navigation-drawer v-if="$route.name!='admin-login'" width="250" light app fixed :clipped="$vuetify.breakpoint.mdAndUp" v-model="drawer">
            <v-list>
                <v-list-tile class="secondary--text"
                    to="/gestion/candidats?api_token=@php echo $_GET['api_token']; @endphp">
                    <v-list-tile-action>
                        <v-icon>supervised_user_circle</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-title>Candidats</v-list-tile-title>
                </v-list-tile>
                @if($role == 1)
                <v-list-tile to="/gestion/ecoles?api_token=@php echo $_GET['api_token']; @endphp">
                    <v-list-tile-action>
                        <v-icon>domain</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-title>Ecoles</v-list-tile-title>
                </v-list-tile>
                @endif
                <v-list-tile to="/gestion/formations?api_token=@php echo $_GET['api_token']; @endphp">
                    <v-list-tile-action>
                        <v-icon>school</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-title>Formations</v-list-tile-title>
                </v-list-tile>
                <v-list-tile to="/gestion/jurys?api_token=@php echo $_GET['api_token']; @endphp">
                    <v-list-tile-action>
                        <v-icon>person_outline</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-title>Jurys</v-list-tile-title>
                </v-list-tile>
                <v-list-tile to="/gestion/entretiens?api_token=@php echo $_GET['api_token']; @endphp">
                    <v-list-tile-action>
                        <v-icon>calendar_today</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-title>Entretiens</v-list-tile-title>
                </v-list-tile>
                @if($role == 1)
                <v-divider></v-divider>
                <v-list-tile to="/gestion/specialites?api_token=@php echo $_GET['api_token']; @endphp">
                    <v-list-tile-title>Spécialités</v-list-tile-title>
                </v-list-tile>
                <v-list-tile to="/gestion/villes?api_token=@php echo $_GET['api_token']; @endphp">
                    <v-list-tile-title>Pays/Villes</v-list-tile-title>
                </v-list-tile>
                <v-list-tile to="/gestion/admins?api_token=@php echo $_GET['api_token']; @endphp">
                    <v-list-tile-title>Administrateurs</v-list-tile-title>
                </v-list-tile>
                @endif
            </v-list>
        </v-navigation-drawer>
        @endif

        <v-content app>
            <router-view class="p-5"></router-view>
        </v-content>
    </v-app>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
    <script async onload="document.querySelector('.v-app').style.visibility='visible'" src="/dist/app.js"></script>
</body>

</html>
