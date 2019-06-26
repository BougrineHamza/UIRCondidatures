<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" href="/images/favicon.png" sizes="16x16" type="image/png">
    <title>UIR - Gestion de candidatures</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="/dist/app.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
</head>
<body>
    <div id="app" class="admin_content">
        <div class="container-fluid">
            <div class="row top_nav p-2 d-flex justify-content-between align-items-center"><img src="/images/uir-logo_white.png" style="width:140px;height:40px;" /><a href="deconnexion?api_token=@php echo $_GET['api_token']; @endphp" class="deconnexion d-flex align-items-center"><i class="material-icons mr-2">power_settings_new</i> Déconnexion</a></div>
            <div class="row">
                <div class="col-md-2 p-0 border-right">
                    <ul class="list-unstyled menu_admin pt-3">
                        <li><a href="candidats?api_token=@php echo $_GET['api_token']; @endphp" ><i class="material-icons">group</i> Candidats</a></li>
                        @if($role == 1)

                        <li><a href="ecoles?api_token=@php echo $_GET['api_token']; @endphp" class="activated"><i class="material-icons">domain</i> Ecoles</a></li>
                        @endif
                        <li><a href="formations?api_token=@php echo $_GET['api_token']; @endphp"><i class="material-icons">school</i> Formations</a></li>
                        <li><a href="jurys?api_token=@php echo $_GET['api_token']; @endphp"><i class="material-icons">person_outline</i> Jurys</a></li>
                        <li><a href="entretiens?api_token=@php echo $_GET['api_token']; @endphp"><i class="material-icons">calendar_today</i> Entretiens</a></li>
                        @if($role == 1)

                        <hr />
                        <li><a href="specialites?api_token=@php echo $_GET['api_token']; @endphp">Spécialités</a></li>
                        <li><a href="villes?api_token=@php echo $_GET['api_token']; @endphp">Pays/Villes</a></li>
                        <li><a href="utilisateurs?api_token=@php echo $_GET['api_token']; @endphp">Administrateurs</a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-10">
                    <div class="container mt-md-4">
                       <ecoles :mes_ecoles="{{ $ecoles }}"></ecoles>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="/dist/app.js"></script>
</body>
</html>