<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cursus;
use App\UirFormation;
use App\UirEcole;
use App\User;


class FormationsController extends Controller
{


    // Modifier mon choix des formations UIR
    public function ModifierFormations(Request $request)
    {
        $this->validate($request, [
            'mes_formations' => 'required|max:30',
            'api_token' => 'required|max:60'
        ]);

        $user = User::where('api_token',$request->api_token)->first();

        $cursus = $user->cursus;

        $cursus->mes_formations_uir = $request->mes_formations;

        $cursus->save();

        return response()->json([
            'status' => 'success'
        ]);


    }


}
