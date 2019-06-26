<?php

namespace App\Http\Controllers;

use App\User;
use App\Cursus;
use Illuminate\Http\Request;

class CursusController extends Controller
{
    // Modifier/Créer Cursus candidat
    public function ModifierCursus(Request $request)
    {
        $this->validate($request, [
            'niveau' => 'required|max:10',
            'annee_diplome' => 'required|numeric',
            'countryedu_id' => 'required|max:30',
            'cityedu_id' => 'required|max:30',
            'systemedu_id' => 'required|max:30',
            'typedu' => 'required|max:30',
            'specialite_id' => 'required|max:50',
            'school' => 'required|max:80',
            'api_token' => 'required|max:60',
        ]);

        $user = User::where('api_token', $request->api_token)->first();

        if (! $user->cursus) {
            $cursus = new Cursus();
            $cursus->user_id = $user->id;
        } else {
            $cursus = Cursus::where('user_id', $user->id)->first();
        }
        // $cursus = Cursus::whereHas('user',function($q) use ($request){
        //     $q->where('api_token', $request->api_token);
        // })->first();

        $cursus->niveau = $request->niveau;
        $cursus->annee_diplome = $request->annee_diplome;
        $cursus->countryedu_id = $request->countryedu_id;
        $cursus->cityedu_id = $request->cityedu_id;
        $cursus->systemedu_id = $request->systemedu_id;
        $cursus->typedu = $request->typedu;
        $cursus->specialite_id = $request->specialite_id;
        $cursus->school = $request->school;
        $cursus->save();

        return response()->json(['status' => 'success']);
    }
}
