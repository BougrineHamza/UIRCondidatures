<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Specialite;
use App\Country;
use App\UirFormation;
use App\UirEcole;
use App\User;
use App\Profil;
use App\ConcourDate;
use Carbon\Carbon;

// Importations
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class DataController extends Controller
{
    // On vérifie si la CIN existe dans notre BDD ou pas
    public function CheckCin(Request $request)
    {
        $this->validate($request, [
            'api_token' => 'required|max:60',
            'cin' => 'max:20'

        ]);

        $cin = Profil::where('cin',$request->cin)->first();

        if($cin){

            if( $cin->user->api_token && $cin->user->api_token == $request->api_token){

                return response()->json(['statut' => true],200);

            } else {

                return response()->json(['statut' => false],200);

            }

        } else {

            return response()->json(['statut' => true],200);

        }

    }

    // Pour avoir la liste des Specialites des profils
    public function IndexSpecialites()
    {
    	$specialites = Specialite::orderBy('titre')->get()
                        ->partition(function ($item) {
                            return $item->titre != 'Autre';
                        })->flatten();

    	$specialites = $specialites->toArray();

        return response()->json($specialites);
    }


    // Pour avoir la liste des Pays et Villes
    public function IndexLocations()
    {   $fractal = new Manager();

        $locations = Country::with(['city' => function($q){
            $q->orderBy('titre')->get();
           }])->orderBy('titre')->get()
                             ->partition(function ($item) {
                                return $item->titre != 'Autre';
                             })->flatten()->toArray();

        $resource = new Collection($locations, function(array $location) {
            return [
                'country'      => $location['titre'],
                'cities'   => $location['city'],
                'nationalite' => $location['nationalite']

            ];
        });

        return $fractal->createData($resource)->toJson();

    }


    // Pour avoir la liste des RDV Disponibles
    public function IndexRDVDispo(Request $request)
    {
        $this->validate($request, [
            'api_token' => 'required|max:60'
        ]);

        $user = User::where('api_token', $request->api_token)->firstOrFail();

        $fractal = new Manager();

        // On ramène les disponibilités des Jours Non Expirés et qui appartiennent soit A NOUS ou à PERSONNE
        $dispos = UirFormation::whereHas('concourdate', function($q) {
            $q->whereDate('date_concours','>=', Carbon::today());
        })->with(['concourdate.concourtime' => function($query) use ($user) {
              $query->where('user_id',null)->orWhere('user_id',$user->id);
            }])->has('concourdate.concourtime')->get()->toArray();

        // $dispos = ConcourDate::whereDate('date_concours','>=', Carbon::today())->with('uirformation')->with(['concourtime' => function($query) use ($user) {
        //       $query->where('user_id',null)->orWhere('user_id',$user->id);
        //     }])->has('concourtime')->get()->toArray();


        $resource = new Collection($dispos, function(array $dispo) {

            return [
                'formation_id'      => $dispo['id'],
                'disponibilites' =>  $dispo['concourdate']

            ];
        });

        return $fractal->createData($resource)->toJson();

    }


    // Pour avoir la liste des formations proposées par UIR
    public function IndexFormations(Request $request)
    {
        $fractal = new Manager();

        $formations = UirFormation::where('condition_specialites',null);

        if($request['api_token'] == 'null'){

            $user_spe = null;

        } else {

            $user = User::where('api_token',$request['api_token'])->first();

            $formations = $formations->orWhere('condition_specialites','like','%'.$user->cursus->specialite_id.'%');
        }


        $formations = $formations->with('uirecole')->get();

        $formations = $formations->toArray();


        $resource = new Collection($formations, function(array $formation) {
            return [
                'formations' => [
                    'id'      => (int) $formation['id'],
                    'titre'   => $formation['titre'],
                    'ecole'    => $formation['uirecole']['titre'],
                    'specialite_matching'    => $formation['condition_specialites'],
                    'code_agresso' => $formation['code_agresso'],
                    'miniature'    => $formation['uirecole']['logo_path'],
                    'niveau_min'    => $formation['condition_niveau'],
                    'specialite_matching'    =>  explode(',',$formation['condition_specialites']),
                    'color'    => $formation['uirecole']['color']
                ],
                'prerequis' => [
                    'formation_id'      => (int) $formation['id'],
                    'contenu' => $formation['prerequis_html']
                ]
            ];
        });

        return $fractal->createData($resource)->toJson();

    }



}
