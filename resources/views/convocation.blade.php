<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" href="/images/favicon.png" sizes="16x16" type="image/png">
    <title>UIR - Convocation entretiens</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/dist/app.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-icons/3.0.1/iconfont/material-icons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
    <style>
      @page {
       size: portrait;
       margin-bottom: 2cm;
       }

    .footer {
      position: fixed;
      width: 100%;
      bottom: 0;
      left: 0;
      right: 0;
    }
    </style>
</head>
<body class="convocation-body">
    <div class="container pdf-container">
        <div class="shadow-lg mt-5 p-4 mb-5 bg-white">
        <div class="col">
        <div class="row pt-3">
            <div class="col-md-6"><img src="{{ url('/images/uir-logo.png') }}" style="width: 190px;" alt="logo-uir"></div>
            <div class="col-md-6 text-right">
                Réf: <b>{{ $convocation->ref }}</b><br />
                Date de création: <b>{{ $convocation->created_at->format('d-m-Y H:i') }}</b><br />
                Statut: <b>@if($convocation->statut) <font class="text-success">Valide</font> @else <font class="text-danger">Annulée</font> @endif</b>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col"><h1 class="font-weight-light text-center text-uppercase">Convocation Entretiens UIR</h1>
                <h5 class="text-center text-capitalize">
                  @if($convocation->user->profil)
                    @if($convocation->user->profil->gender)
                    M.
                    @else
                    Mlle./Mme.
                    @endif
                     {{ $convocation->user->first_name}} {{ $convocation->user->last_name}} - (CIN: {{ $convocation->user->profil->cin}})
                   @endif
                 </h5>
            </div>
        </div>

        <div class="row my-5"><div class="col"><h4 class="text-uppercase text-center"><font class="text-muted">Vous avez </font><font class="text-green"> {{$concours_times->count() }} entretien(s)</font></h4></div></div>


        <div class="table-responsive mb-4">
              <table class="table table-bordered  table-hover ">
                <thead class="thead-light">
                    <tr>
                      <th scope="col">Ecole</th>
                      <th scope="col">Formation</th>
                      <th scope="col">Date Entretien</th>
                      <th scope="col">Emplacement Entretien</th>

                    </tr>
              </thead>
              <tbody class="table-striped">

                @php $archi = 0; $dent = 0; @endphp
        @foreach($concours_times as $rdv)

                @if($rdv->uirformation->uirecole->id == 8)
                @php
                  $archi = 1;
                @endphp

                @elseif($rdv->uirformation->uirecole->id == 9)
                @php
                  $dent = 1;
                @endphp
                @endif

                <tr style="border-top:2px solid black;">
                  <td><img width="100" src="{{ url('/images/'.$rdv->uirformation->uirecole->logo_path) }}"></td>
                  <td>{{ $rdv->uirformation->titre }}</td>
                  <th scope="row">
                    @php
                    $d = strtotime($rdv->concourdate->date_concours);
                    $t = strtotime($rdv->time);
                    echo date('d/m/Y',$d);
                    echo ' à '.date('H:i',$t);
                    @endphp
                  </th>
                  <td>
                    @if($rdv->uirformation->uirecole->emplacement)
                      {{ $rdv->uirformation->uirecole->emplacement }}
                    @else
                    ---
                    @endif
                  </td>
                </tr>
                <tr class="" style="background: #f8faff;">
                  <td colspan="4">
                    <b>PRÉREQUIS:</b><br />
                    <font style="font-size:14px !important;">{!! $rdv->uirformation->prerequis_html !!}</font>
                </td>
                </tr>

        @endforeach
        </tbody>
              </table>
            </div>

            <h6 class="text-uppercase font-weight-bold text-danger">Pièces à fournir</h6>
            @if($archi == 1)
            <div class="text-08 no-gutters row">
                  <b>Pièces à fournir pour l'école d'Architecture de Rabat:</b>
                  <ul>
                    <li>Lettre de demande d'entrée par équivalence adressée à Mme la Directrice de l'école d'architecture de l'université internationale de Rabat, et précisant le niveau demandé</li>
                    <li>Le CV précisant adresse, email et numéro de téléphone portable</li>
                    <li>Une copie certifiée conforme du certificat du baccalauréat</li>
                    <li>Une copie certifiée conforme du relevé de notes du baccalauréat</li>
                    <li>Une copie certifiée conforme de l'attestation de réussite des années d'étude précédant le niveau demandé</li>
                    <li>Les copies certifiées conformes de tous les relevés de notes des années post-
                    baccalauréat, cachetés et signés par le directeur des études ou le directeur pédagogique</li>
                    <li>Le programme des études des années passées en école d'architecture</li>
                    <li>Le BO dans lequel apparaît la reconnaissance par le Ministère de l'enseignement
                    supérieur de l'école d'architecture d’origine</li>
                    <li>Une photocopie de la Carte d'identité nationale (ou du passeport pour les étudiants
                    étrangers)</li>
                  </ul>
            </div>
            @endif

            @if($dent == 1)
            <div class="text-08 no-gutters row">
                  <b>Pièces à fournir pour la Faculté Internationale de Médecine Dentaire de Rabat:</b>
                  <ul>
                    <li>Lettre de motivation pour l’adhésion à la formation de la FIMD</li>
                  </ul>
            </div>
            @endif

            <div class="row pt-4 no-gutters" style="border-top:1px solid rgba(0,0,0,0.1);">
              <div class="col-md-7 text-08" style="line-height: 1.4;font-weight: bold;">
                Veuillez vous présenter à l’université muni des pièces suivantes pour chaque entretien:
                <ul>
                  <li>Copie CIN ou passeport</li>
                  <li>CV actualisé</li>
                  <li>Une copie du diplôme du baccalauréat</li>
                  <li>Copie du dernier diplôme obtenu ou attestation de réussite ou attestation d’inscription de l’année en cours</li>
                  <li>Copies des relevés de notes de vos études supérieures</li>
                </ul>
                <b class="text-danger">* PS: Rammener un dossier par Master choisi.</b>
              </div>
              <div class="col-md-5 text-08 text-md-right text-left" style="line-height: 1.4">
                <b class="text-uppercase">Adresse entretien</b>
                <p>Campus de l’UIR, Parc Technopolis<br />
                    Rocade de Rabat-Salé<br />
                    11100 – Sala Al Jadida – Maroc</p>
              </div>
            </div>

            <div class="d-none d-md-block">
            <table class="table small text-muted mb-0" style="line-height:1.2; font-size:0.8em;">
              <tbody>
                <tr>
                  <td><img src="{{ url('/images/uir-care.jpg') }}" style="margin-top:6px;width: 30px;" alt="logo-uir"></td>
                  <td>UIR - Université Internationale de Rabat<br />Technopolis Rabat-Shore, Rocade Rabat-Salé.<br />11 100 Sala el Jadida - Maroc.</td>
                  <td>Tel : +212530103000<br />Support: Concours@uir.ac.ma<br />www.uir.ac.ma</td>
                </tr>
              </tbody>
            </table>
            </div>

            <div class="footer d-md-none d-block" style="background: rgba(255,255,255,0.9)">
            <table class="table small text-muted" style="line-height:1; font-size:0.7em; margin:0;">
              <tbody>
                <tr>
                  <td><img src="{{ url('/images/uir-care.jpg') }}" style="margin-top:6px;width: 30px;" alt="logo-uir"></td>
                  <td>UIR - Université Internationale de Rabat<br />Technopolis Rabat-Shore, Rocade Rabat-Salé.<br />11 100 Sala el Jadida - Maroc.</td>
                  <td>Tel : +212530103000<br />Support: Concours@uir.ac.ma<br />www.uir.ac.ma</td>
                </tr>
              </tbody>
            </table>
            </div>


        </div>
        </div>
    </div>
</body>
</html>