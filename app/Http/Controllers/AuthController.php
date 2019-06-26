<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Stat;
use App\Profil;
use App\Cursus;
use App\ConcourTime;
use App\Cloned;
use Illuminate\Support\Facades\Hash; // Pour Hasher le mot de passe
// Importations
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;



class AuthController extends Controller
{
	// On check le degrès de validité de l'email
	// PS: Cette fonction sera appelé au niveau d'autres fonctions, notamment INSCRIPT et Paramètres -> CHANGER EMAIL
	public function MailCheck(User $user)
	{
        // On contacte via Guzzle l'API de NeverBounce Online pour avoir la réponse du degrès
        $client = new \GuzzleHttp\Client();

        // On contacte Neverbounce via API pour avoir une réponse sur le degrès de validité de l'email
        $res = $client->get(
			'https://api.neverbounce.com/v4/single/check',
			['query' =>['email' => $user->email,
			'key' => 'private_1d585fc371c1a5b8f3232a5caafb57f7']
		]);

        // On jsonify le résultat obtenu et en sort uniquement l'attribut Result
        $mail_bounce_level = json_decode($res->getBody())->result;

        // On affecte le résultat à l'utilisateur au niveau de la BDD
        // $user = User::where('email',$email)->firstOrFail();

        $user->email_bounced = $mail_bounce_level;

        $user->save();

        // On retourne la réponse pour des usages autres
        return response()->json(['statut' => 'succes','Email_check' => $mail_bounce_level]);

	}

	// Authentification O365
	public function o365(Request $request)
	{
		$this->validate($request, [
            'email' => 'required|email|max:80',
            'password' => 'required|max:60'
        ]);

		// On valide que c'est bien un e-mail UIR
        if (strpos($request->email, '@uir.ac.ma') === false) {
		    return response()->json(['statut' => 'Ce n\'est pas un email UIR'],400);
		}

		// On contacte via Guzzle l'API de Microsoft Online pour avoir l'access_token
        $client = new \GuzzleHttp\Client();

		$res = $client->post(
			'https://login.microsoftonline.com/organizations/oauth2/v2.0/token',
			['form_params' =>
			['grant_type' => 'password',
			 'username' => $request->email,
			 'password' => $request->password,
			 'client_id' => '00970931-21bf-46bc-9be9-f460d43fb6b7',
			 'scope' => 'openid User.Read'
			]
			]);

		$body = json_decode($res->getBody());


		// On authentifie l'utilisateur sur AD avec l'access_token pour ainsi avoir le Numéro Etudiant
		$headers = [
		    'Authorization' => 'Bearer ' . $body->access_token,
		    'Accept'        => 'application/json',
		];

		$newresponse = $client->get(
	    'https://graph.microsoft.com/v1.0/me',
		[
	     'headers' => $headers
	    ]
		)->getBody()->getContents();

		$num_etudiant = json_decode($newresponse)->officeLocation;


		// Mtnt que nous avons le numéro étudiant on procède à l'authentification sur l'application

		if($num_etudiant){

			$user_clone = Cloned::where('num_etudiant',$num_etudiant)->first();

			$user_uir = User::where('email',$request->email)->first();


			// On vérifie si le clone existe dans notre BDD
			if($user_clone) {

				// On vérifie si l'étudiant UIR est inscrit ou pas
				if($user_uir){

					// On ouvre une session Token à l'utilisateur
					$user_uir->update(['api_token' => str_random(60)]);

					// On met à jour des statistiques de l'utilisateur
					$user_uir->stat->update([
						'last_login' => date('Y-m-d H:i'),
						'total_connexions' => $user_uir->stat->total_connexions + 1,
						'last_ip' => '0.0.0.0'
					]);

					return response()->json(['lead_level' => $user_uir->stat->lead_level,'user' => $user_uir, 'statut' => 'a_connecter'],200);

				} else {

					// On crée le nouvel utilisateur à inscrire
					$user_a_inscrire = new User();

			        $user_a_inscrire->first_name = $user_clone->first_name;

					$user_a_inscrire->last_name = $user_clone->last_name;

					$user_a_inscrire->email = $request->email;

					$user_a_inscrire->password = Hash::make($request->password);

					$user_a_inscrire->api_token = str_random(60);

					$user_a_inscrire->uir_student = 1;

					$user_a_inscrire->email_confirmed = 1;

					$user_a_inscrire->email_bounced = 'uir_valid';

					$user_a_inscrire->save();


					// On enregistre le profil de l'étudiant
					$profil_a_inscrire = new Profil();

					$profil_a_inscrire->user_id = $user_a_inscrire->id;

					$profil_a_inscrire->cin = $user_clone->cin;

					$profil_a_inscrire->gender = $user_clone->gender;

					$profil_a_inscrire->country_id = $user_clone->country_id;

					$profil_a_inscrire->city_id = $user_clone->city_id;

					$profil_a_inscrire->address = $user_clone->address;

					$profil_a_inscrire->phone = $user_clone->phone;

					$profil_a_inscrire->save();


					// On enregistre le cursus de l'étudiant
					$cursus_a_inscrire = new Cursus();

					$cursus_a_inscrire->user_id = $user_a_inscrire->id;

					$cursus_a_inscrire->niveau = $user_clone->niveau;

					$cursus_a_inscrire->annee_diplome = $user_clone->annee_diplome;

					$cursus_a_inscrire->countryedu_id = 'Maroc';

					$cursus_a_inscrire->cityedu_id = 'Rabat';

					$cursus_a_inscrire->systemedu_id = 'Marocain';

					$cursus_a_inscrire->typedu = 'Privé';

					$cursus_a_inscrire->specialite_id = $user_clone->specialite_id;

					$cursus_a_inscrire->school = 'Université Internationale de Rabat';

					$cursus_a_inscrire->save();


					// On comence le rassemblement de statistiques sur l'utilisateur
					$stat = new Stat();
			        $stat->user_id = $user_a_inscrire->id;
					$stat->last_login =  date('Y-m-d H:i');
					$stat->total_connexions = 1;
					$stat->lead_level = 2;
					$stat->last_ip = '0.0.0.0';
			        $stat->save();


			        return response()->json(['statut' => 'inscrit_succes','user' => $user_a_inscrire,'profil' => $profil_a_inscrire,'cursus' => $cursus_a_inscrire],200);
				}

			} else {

				if($user_uir){

					// On ouvre une session Token à l'utilisateur
					$user_uir->update(['api_token' => str_random(60)]);

					// On met à jour certaines statistiques de l'utilisateur
					$user_uir->stat->update([
						'last_login' => date('Y-m-d H:i'),
						'total_connexions' => $user_uir->stat->total_connexions + 1,
						'last_ip' => '0.0.0.0'
					]);

					return response()->json(['lead_level' => $user_uir->stat->lead_level,'user' => $user_uir, 'statut' => 'a_connecter'],200);

				} else {
					// On renvoit que l'utilisateur n'est ni inscrit, ni détient un clone
					return response()->json(['statut' => 'clone_inexistant'],200);
				}
			}

		} else {
			return response()->json(['statut' => 'identifiants_incorrects', 'num_etudiant' => null,400]);
		}

	}


	// Inscription d'un nouveau candidat UIR (avec connexion)
	public function Inscription(Request $request)
	{
		$this->validate($request, [
            'first_name' => 'required|max:30',
            'last_name' => 'required|max:30',
            'email' => 'required|email|unique:users|max:60',
            'password' => 'required|max:60'
        ]);

		// On vérifie si l'adresse email est celle d'un étudiant UIR ou non
		if (strpos($request->email, 'uir.ac.ma') !== false) {

			$user_clone = Cloned::where('email',$request->email)->first();

			if($user_clone){
				return response()->json(['statut' => 'existant'],200);
			}

		    $uir_student = 1;
		} else {
			$uir_student = 0;
		}

		// On crée le nouvel utilisateur à inscrire
		$user = new User();

        $user->first_name = $request->first_name;

		$user->last_name = $request->last_name;

		$user->email = $request->email;

		$user->password = Hash::make($request->password);

		$user->api_token = str_random(60);

		$user->uir_student = $uir_student;

		$user->save();


		// On comence le rassemblement de statistiques sur l'utilisateur
		$stat = new Stat();
        $stat->user_id = $user->id;
		$stat->last_login =  date('Y-m-d H:i');
		$stat->total_connexions = 1;
		$stat->last_ip = '0.0.0.0';
        $stat->save();


		// // On renseigne le degrès de validité de l'email au niveau de la BDD
		// $this->MailCheck($user);

        return response()->json(['status' => 'success','user' => $user],201);

	}


	// Connexion du candidat qu'il soit UIR ou non UIR
	public function Connexion(Request $request)
	{

		$this->validate($request, [
            'email' => 'required|email|max:60',
            'password' => 'required|max:60'
        ]);


		$user = User::where('email',$request->email)->first();

		if(!$user){
        	return response()->json(['status' => 'error','message' => 'Utilisateur inexistant'],401);
		}

		if(Hash::check($request->password, $user->password)){

			$user->update(['api_token' => str_random(60)]);

			$user->stat->update([
				'last_login' => date('Y-m-d H:i'),
				'total_connexions' => $user->stat->total_connexions + 1,
				'last_ip' => '0.0.0.0'
			]);


         return response()->json(['lead_level' => $user->stat->lead_level,'user' => $user],200);


		} else {

			return response()->json(['status' => 'error','message' => 'Mot de passe incorrect'],401);

		}



	}


	// Deconnexion du compte Candidat
	public function Deconnexion(Request $request)
	{
        $this->validate($request, [
            'api_token' => 'required|max:60'
        ]);

		$user = User::where('api_token', $request->api_token)->first();

		if($user){

			$user->api_token = null;

			$user->save();

			return response()->json(['status' => 'success','message' => 'Utilisateur déconnecté avec succès'],200);


		} else {

			return response()->json(['status' => 'error','message' => 'Utlisateur introuvable'],401);
		}


	}


    // Modifier l'email de connexion au compte
    public function ModifierEmail(Request $request){
    	$this->validate($request, [
            'api_token' => 'required|max:60',
            'newemail' => 'required|email'
        ]);

		$user = User::where('api_token', $request->api_token)->first();

		$exists = User::where('email',$request->newemail)->exists();

        if($user && !$exists){

        	$user->email = $request->newemail;

        	$user->save();

			// On renseigne le degrès de validité de l'email au niveau de la BDD
			$this->MailCheck($user);

			return response()->json(['status' => 'succes'],200);


        } else {

			return response()->json(['status' => 'error'],400);

        }

    }

    // Modifier le mot de passe de connexion au compte
    public function ModifierMdp(Request $request){

    	$this->validate($request, [
            'api_token' => 'required|max:60',
            'newpass' => 'required|max:15'
        ]);

		$user = User::where('api_token', $request->api_token)->first();

        if($user){

        	$user->password = Hash::make($request->newpass);

        	$user->save();

			return response()->json(['status' => 'succes'],200);


        } else {

			return response()->json(['status' => 'error'],400);

        }



    }


}
