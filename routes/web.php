<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

// Fonctions
$router->get('/', function () use ($router) {
    return view('app');
});


// Privées ADMIN avec Auth API
$router->group(['prefix' => 'sms'], function ($router) {
});

###### DEBUT Fonction pour ecraser des horaires d'entretiens comme demandé par Said
// $router->get('/reset-entretiens/{original}/{replaced}', function ($original,$replaced) use ($router) {
//     $from = date('2019-05-07');
//     $to = date('2019-05-31');
//     $entretiens = \App\ConcourTime::whereHas('concourdate', function($query) use ($from,$to){
//         $query->whereBetween('date_concours', [$from, $to]);
//     })->whereHas('uirformation.uirecole', function($q){
//         $q->where('id',1);
//     })->get();

//     $test = null;
//     foreach($entretiens as $entretien){
//         if($entretien->time == $original){
//             $entretien->time = $replaced;
//             $entretien->save();
//             $test = $test+1;
//         }
//     }
//     dd($test);
// });
######## FIN


###### DEBUT Envoyer des emails à des utilisateurs ayant un entretien Entre Date et Date
// $router->get('/sendmail', function () use ($router) {
//     $from = date('2019-05-07');
//     $to = date('2019-05-31');
//     $users = \App\User::whereHas('concourtime.concourdate', function($query) use ($from,$to){
//         $query->whereBetween('date_concours', [$from, $to]);
//     })->get();

//     $test = null;
//     foreach($users as $user){

//             \Illuminate\Support\Facades\Mail::to($user)->send(new \App\Mail\NotifHoraireRamadan($user->email,$user->password));
//     }

//     dd('done');
// });
######## FIN


###### DEBUT Envoyer des emails à des utilisateurs ayant un entretien Entre Date et Date
// $router->get('/sendmailpb', function () use ($router) {
//     $from = date('2019-05-07');
//     $to = date('2019-05-31');
//     $users = \App\User::whereHas('concourtime.concourdate', function($query) use ($from,$to){
//         $query->whereBetween('date_concours', [$from, $to]);
//     })->get();

//     $test = null;
//     foreach($users as $user){

//             \Illuminate\Support\Facades\Mail::to($user)->send(new \App\Mail\ProblemeTechnique($user->email,$user->password));
//     }

//     dd('done');
// });
######## FIN

// Privées ADMIN avec Auth API
$router->group(['prefix' => 'gestion', 'middleware' => 'auth_admin'], function ($router) {
    // Debut ADMIN Routes
    //GET
    $router->get('candidats', ['uses' => 'AdminController@Candidats']);
    $router->get('api/candidats', ['uses' => 'AdminController@apiCandidats']);
    $router->get('api/ecoles', ['uses' => 'AdminController@apiEcoles']);
    $router->get('api/formations', ['uses' => 'AdminController@apiFormations']);
    $router->get('api/jurys', ['uses' => 'AdminController@apiJurys']);
    $router->get('api/entretiens', ['uses' => 'AdminController@apiEntretiens']);
    $router->get('api/getformasjurys', ['uses' => 'AdminController@apiGetformasjurys']);
    $router->get('api/specialites', ['uses' => 'AdminController@apiSpecialites']);
    $router->get('api/villes', ['uses' => 'AdminController@apiVilles']);
    $router->get('api/utilisateurs', ['uses' => 'AdminController@apiUsers']);

    // $router->get('ecoles', ['uses' => 'AdminController@Ecoles']);
    // $router->get('formations', ['uses' => 'AdminController@Formations']);
    // $router->get('jurys', ['uses' => 'AdminController@Jurys']);
    // $router->get('entretiens', ['uses' => 'AdminController@Entretiens']);
    // $router->get('getformasjurys', ['uses' => 'AdminController@GetFormasJurys']);
    // $router->get('specialites', ['uses' => 'AdminController@Specialites']);
    // $router->get('villes', ['uses' => 'AdminController@Villes']);
    // $router->get('utilisateurs', ['uses' => 'AdminController@Users']);

    $router->get('ecoles', ['uses' => 'AdminController@Candidats']);
    $router->get('formations', ['uses' => 'AdminController@Candidats']);
    $router->get('jurys', ['uses' => 'AdminController@Candidats']);
    $router->get('entretiens', ['uses' => 'AdminController@Candidats']);
    $router->get('getformasjurys', ['uses' => 'AdminController@GetFormasJurys']);
    $router->get('specialites', ['uses' => 'AdminController@Candidats']);
    $router->get('villes', ['uses' => 'AdminController@Candidats']);
    $router->get('utilisateurs', ['uses' => 'AdminController@Candidats']);
    $router->get('admins', ['uses' => 'AdminController@Candidats']);

    $router->post('deconnexion', ['uses' => 'AdminController@Deconnexion']);

    // GetUnique
    $router->get('ecole', ['uses' => 'AdminController@Ecole']);
    $router->get('formation', ['uses' => 'AdminController@Formation']);
    $router->get('jury', ['uses' => 'AdminController@Jury']);
    $router->get('entretien', ['uses' => 'AdminController@Entretien']);
    $router->get('specialite', ['uses' => 'AdminController@Specialite']);
    $router->get('ville_u', ['uses' => 'AdminController@Ville']);
    $router->get('pays_u', ['uses' => 'AdminController@Country']);
    $router->get('utilisateur', ['uses' => 'AdminController@User']);
    $router->get('utilisateur_t', ['uses' => 'AdminController@UserToken']);
    $router->get('convocation', ['uses' => 'AdminController@VoirConvocation']);

    //POST
    $router->post('api/import', ['uses' => 'AdminController@apiImport']);
    $router->post('ecoles', ['uses' => 'AdminController@EcolesAdd']);
    $router->post('formations', ['uses' => 'AdminController@FormationsAdd']);
    $router->post('jurys', ['uses' => 'AdminController@JurysAdd']);
    $router->post('entretiens', ['uses' => 'AdminController@EntretiensAdd']);
    $router->post('specialites', ['uses' => 'AdminController@SpecialitesAdd']);
    $router->post('villes', ['uses' => 'AdminController@VillesAdd']);
    $router->post('pays', ['uses' => 'AdminController@PaysAdd']);
    $router->post('utilisateurs', ['uses' => 'AdminController@UsersAdd']);

    //UPDATE
    $router->put('ecoles', ['uses' => 'AdminController@EcolesUpdate']);
    $router->put('formations', ['uses' => 'AdminController@FormationsUpdate']);
    $router->put('jurys', ['uses' => 'AdminController@JurysUpdate']);
    $router->put('api/entretien', ['uses' => 'AdminController@EntretienUpdate']);
    $router->put('entretiens', ['uses' => 'AdminController@EntretiensUpdate']);
    $router->put('specialites', ['uses' => 'AdminController@SpecialitesUpdate']);
    $router->put('villes', ['uses' => 'AdminController@VillesUpdate']);
    $router->put('pays', ['uses' => 'AdminController@PaysUpdate']);
    $router->put('utilisateurs', ['uses' => 'AdminController@UsersUpdate']);

    //DELETE
    $router->delete('ecoles', ['uses' => 'AdminController@EcolesDelete']);
    $router->delete('formations', ['uses' => 'AdminController@FormationsDelete']);
    $router->delete('jurys', ['uses' => 'AdminController@JurysDelete']);
    $router->delete('entretiens', ['uses' => 'AdminController@EntretiensDelete']);
    $router->delete('specialites', ['uses' => 'AdminController@SpecialitesDelete']);
    $router->delete('villes', ['uses' => 'AdminController@VillesDelete']);
    $router->delete('pays', ['uses' => 'AdminController@PaysDelete']);
    $router->delete('utilisateurs', ['uses' => 'AdminController@UsersDelete']);

    // Fin ADMIN Routes
});
// Authentification des Administrateurs
$router->get('/gestion/connexion',['uses' => 'AdminController@Candidats']);
$router->post('/gestion/connexion_encours', ['uses' => 'AdminController@Connexion']);
// Chek if admin is the one who's making the request
$router->get('authAdmin', ['uses' => 'AdminController@AuthAdmin']);

$router->group(['prefix' => 'email'], function ($router) {

    // Email OutBound
    $router->post('convocation-creee', ['uses' => 'EmailController@ConvocationCreee']);

    $router->post('envoyer-mdpoublie', ['uses' => 'EmailController@NouveauMDP']);

    $router->post('envoyer-emailconfirmation', ['uses' => 'EmailController@EnvoyerEmailConfirmation']);

    // Email InBound
    $router->get('mes-convocations', ['uses' => 'EmailController@PageConvocationCreee']);

    $router->get('modifer-mdp', ['uses' => 'EmailController@PageNouveauMDP']);

    $router->get('confirmer-email', ['uses' => 'EmailController@PageConfirmerEmail']);

});

$router->group(['prefix' => 'api'], function ($router) {

    // Privées qui demandent une auth API Token
    $router->group(['prefix' => 'candidat', 'middleware' => 'auth'], function ($router) {

        // POST routes
        $router->post('deconnexion', ['uses' => 'AuthController@Deconnexion']);

        $router->post('modifier-profil', ['uses' => 'ProfilController@ModifierProfil']);

        $router->post('modifier-cursus', ['uses' => 'CursusController@ModifierCursus']);

        $router->post('modifier-mesformations', ['uses' => 'FormationsController@ModifierFormations']);

        $router->post('modifier-mesrdv', ['uses' => 'RdvController@ModifierRdv']);

        $router->post('modifer-email', ['uses' => 'AuthController@ModifierEmail']);

        $router->post('modifer-mdp', ['uses' => 'AuthController@ModifierMdp']);

        $router->post('modifier-niveau', ['uses' => 'UserController@ModifierNiveauLead']);

        // GET routes

        $router->get('rdv-disponibles', ['uses' => 'DataController@IndexRDVDispo']);

        $router->get('mes-convocations', ['uses' => 'RdvController@IndexMesConvocations']);

        $router->get('mes-rdv', ['uses' => 'RdvController@IndexMesRdv']);

        $router->get('infos-utilisateur', ['uses' => 'UserController@GetUserInfo']);

        $router->get('voir-convocation/{id}', ['uses' => 'RdvController@ShowConvocation']);

        $router->get('convocation-pdf/{id}', ['uses' => 'RdvController@TelechargerConvocation']);

        $router->get('check_cin', ['uses' => 'DataController@CheckCin']);

    });

    // Publiques sans auth API Token
    // POST routes
    $router->post('connexion', ['uses' => 'AuthController@Connexion']);

    $router->post('inscription', ['uses' => 'AuthController@Inscription']);

    $router->post('auth365', ['uses' => 'AuthController@o365']);

    // GET routes
    $router->get('specialites', ['uses' => 'DataController@IndexSpecialites']);

    $router->get('localisations', ['uses' => 'DataController@IndexLocations']);

    $router->get('formations', ['uses' => 'DataController@IndexFormations']);

});

$router->get('/{route:.*}/', function () {
    return view('app');
});
