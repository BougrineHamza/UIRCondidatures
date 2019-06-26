<?php

namespace App\Http\Controllers;

use App\Stat;
use App\User;
use League\Fractal\Manager;
// Importations
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;

class UserController extends Controller
{
    // Modifier le niveau Lead du candidat (Niveau completion du workflow)
    public function ModifierNiveauLead(Request $request)
    {
        $this->validate($request, [
            'api_token' => 'required|max:60',
            'niveau' => 'required|numeric',
        ]);

        $stat = Stat::whereHas('user', function ($q) use ($request) {
            $q->where('api_token', $request->api_token);
        })->first();

        $stat->lead_level = $request->niveau;

        $stat->save();

        return response()->json(['status' => 'success']);
    }

    // Rapporter les informations sur l'utilisateur connecté (Ex: LeadLevel)
    public function GetUserInfo(Request $request)
    {
        $this->validate($request, [
            'api_token' => 'required|max:60',
        ]);

        $user = User::where('api_token', $request->api_token)->first();

        if ($user) {
            if ($user->stat->lead_level == 0) {
                // Utilisateur inscrit uniquement
                return response()->json([
                    'user' => [
                        'id' => $user->id,
                        'email_confirmed' => $user->email_confirmed,
                        'uir_student' => $user->uir_student,
                        'firstname' => $user->first_name,
                        'lastname' => $user->last_name,
                        'email' => $user->email,
                        'lead_level' => 0,
                    ],
                ]);
            } elseif ($user->stat->lead_level == 1) {
                // Utilisateur ayant créé son profil
                return response()->json([
                    'user' => [
                        'id' => $user->id,
                        'email_confirmed' => $user->email_confirmed,
                        'uir_student' => $user->uir_student,
                        'firstname' => $user->first_name,
                        'lastname' => $user->last_name,
                        'email' => $user->email,
                        'lead_level' => 1,
                    ],
                    'profil' => [
                          'sexe' => $user->profil->gender,
                          'cin' => $user->profil->cin,
                          'country' => $user->profil->country_id,
                          'city' => $user->profil->city_id,
                          'address' => $user->profil->address,
                          'gsm' => $user->profil->phone,
                          'cndp' => 1,
                    ],
                ]);
            } elseif ($user->stat->lead_level == 2) {
                // Utilisateur ayant renseigné son Cursus de formation
                return response()->json([
                    'user' => [
                        'id' => $user->id,
                        'email_confirmed' => $user->email_confirmed,
                        'uir_student' => $user->uir_student,
                        'firstname' => $user->first_name,
                        'lastname' => $user->last_name,
                        'email' => $user->email,
                        'lead_level' => 2,
                    ],
                    'profil' => [
                          'sexe' => $user->profil->gender,
                          'cin' => $user->profil->cin,
                          'country' => $user->profil->country_id,
                          'city' => $user->profil->city_id,
                          'address' => $user->profil->address,
                          'gsm' => $user->profil->phone,
                          'cndp' => 1,
                    ],
                    'cursus' => [
                          'niveau' => $user->cursus->niveau,
                          'annee_diplome' => $user->cursus->annee_diplome,
                          'country_etudes' => $user->cursus->countryedu_id,
                          'city_etudes' => $user->cursus->cityedu_id,
                          'systeme_etudes' => $user->cursus->systemedu_id,
                          'type_institut' => $user->cursus->typedu,
                          'etablissment' => $user->cursus->school,
                          'specialite' => $user->cursus->specialite_id,
                    ],
                ]);
            } elseif ($user->stat->lead_level == 3) {
                // Utilisateur ayant choisi des formations UIR
                return response()->json(['user' => [
                        'id' => $user->id,
                        'email_confirmed' => $user->email_confirmed,
                        'uir_student' => $user->uir_student,
                        'firstname' => $user->first_name,
                        'lastname' => $user->last_name,
                        'email' => $user->email,
                        'lead_level' => 3,
                    ],
                    'profil' => [
                          'sexe' => $user->profil->gender,
                          'cin' => $user->profil->cin,
                          'country' => $user->profil->country_id,
                          'city' => $user->profil->city_id,
                          'address' => $user->profil->address,
                          'gsm' => $user->profil->phone,
                          'cndp' => 1,
                    ],
                    'cursus' => [
                          'niveau' => $user->cursus->niveau,
                          'annee_diplome' => $user->cursus->annee_diplome,
                          'country_etudes' => $user->cursus->countryedu_id,
                          'city_etudes' => $user->cursus->cityedu_id,
                          'systeme_etudes' => $user->cursus->systemedu_id,
                          'type_institut' => $user->cursus->typedu,
                          'etablissment' => $user->cursus->school,
                          'specialite' => $user->cursus->specialite_id,
                    ],
                    'formations_choisies' => array_map('intval', explode(',', $user->cursus->mes_formations_uir)),

                    ]);
            } elseif ($user->stat->lead_level == 4) {
                $myrdv_time_id = [];
                $myforma_time_id = [];
                $mydates_time_id = [];

                foreach ($user->concourtime as $rdv) {
                    array_push($myrdv_time_id, $rdv->id);

                    if (! in_array($rdv->uirformation->id, $myforma_time_id)) {
                        array_push($myforma_time_id, $rdv->uirformation->id);
                    }

                    if (! in_array($rdv->concourdate->date_concours.'-'.$rdv->time, $mydates_time_id)) {
                        array_push($mydates_time_id, $rdv->concourdate->date_concours.'-'.$rdv->time);
                    }
                }

                // Utilisateur ayant choisi créé une Convocation
                return response()->json(['user' => [
                        'id' => $user->id,
                        'email_confirmed' => $user->email_confirmed,
                        'uir_student' => $user->uir_student,
                        'firstname' => $user->first_name,
                        'lastname' => $user->last_name,
                        'email' => $user->email,
                        'lead_level' => 4,
                    ],
                    'profil' => [
                          'sexe' => $user->profil->gender,
                          'cin' => $user->profil->cin,
                          'country' => $user->profil->country_id,
                          'city' => $user->profil->city_id,
                          'address' => $user->profil->address,
                          'gsm' => $user->profil->phone,
                          'cndp' => 1,
                    ],
                    'cursus' => [
                          'niveau' => $user->cursus->niveau,
                          'annee_diplome' => $user->cursus->annee_diplome,
                          'country_etudes' => $user->cursus->countryedu_id,
                          'city_etudes' => $user->cursus->cityedu_id,
                          'systeme_etudes' => $user->cursus->systemedu_id,
                          'type_institut' => $user->cursus->typedu,
                          'etablissment' => $user->cursus->school,
                          'specialite' => $user->cursus->specialite_id,
                    ],
                    'formations_choisies' => array_map('intval', explode(',', $user->cursus->mes_formations_uir)),
                    'myrdv_time_id' => $myrdv_time_id,
                    'myforma_time_id' => $myforma_time_id,
                    'mydates_time_id' => $mydates_time_id,
                    'mes_convocations' => $user->convocation,
                    ]);
            }
        } else {
            return response()->json(['status' => 'erreur']);
        }
    }
}
