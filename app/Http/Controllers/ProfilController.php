<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profil;
use App\User;


class ProfilController extends Controller
{


	// Création / Modification du profil Candidat
	public function ModifierProfil(Request $request){
		$this->validate($request, [
            'gender' => 'required|boolean',
            'cin' => 'required',
            'country_id' => 'required',
            'city_id' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'api_token' => 'required'
        ]);

        $user = User::where('api_token', $request->api_token)->first();

        $cin = Profil::where('cin',$request->cin)->first();

        if($cin){
            if($cin->user->api_token != $request->api_token){

                return response()->json(['statut' => false],200);
            }

        }

        $profile = $user->profil;

        if(!$profile){
        	$profile = new Profil();

        	$profile->user_id = $user->id;
        }

        $profile->gender = $request->gender;

        $profile->cin = $request->cin;

        $profile->country_id = $request->country_id;

        $profile->city_id = $request->city_id;

        $profile->address = $request->address;

        $profile->phone = $request->phone;

        $profile->save();


        if($profile->user->stat->lead_level < $request->lead_level){

        	$profile->stat->lead_level = $request->lead_level;

        	$profile->stat->save();
        }

        return response()->json(['status' => 'success']);


	}


}

