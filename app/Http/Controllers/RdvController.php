<?php

namespace App\Http\Controllers;

use App\User;
use App\ConcourTime;
use App\Convocation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Collection;

class RdvController extends Controller
{
    // Lister toutes mes convocations créées
    public function IndexMesConvocations(Request $request)
    {
        $this->validate($request, [
            'api_token' => 'required|max:60',
        ]);

        $convocations = Convocation::whereHas('user', function ($q) use ($request) {
            $q->where('api_token', $request->api_token);
        })->get();

        return response()->json(['statut'=>'success', 'convocations' => $convocations], 200);
    }

    // Générateur de Template des convocations en HTML
    public function ShowConvocation(Request $request)
    {
        $this->validate($request, [
                    'api_token' => 'required|max:60',
                ]);

        // On s'assure que l'ID de la convo communiqué appartient réellement au détenteur de l'API Token
        $convocation = Convocation::whereHas('user', function ($q) use ($request) {
            $q->where('api_token', $request->api_token);
        })->findOrFail($request->id);

        // On ramène les informations à afficher sur la Convocation
        $concours_times = ConcourTime::findMany(explode(',', $convocation->concour_times_ids));

        return view('convocation', compact(['convocation', 'concours_times']));
    }

    // Télécharger les PDF des convocations
    public function TelechargerConvocation(Request $request)
    {
        $this->validate($request, [
                    'api_token' => 'required|max:60',
                ]);

        // On s'assure que l'ID de la convo communiqué appartient réellement au détenteur de l'API Token
        $convocation = Convocation::whereHas('user', function ($q) use ($request) {
            $q->where('api_token', $request->api_token);
        })->findOrFail($request->id);

        // On ramène les informations à afficher sur la Convocation
        $concours_times = ConcourTime::findMany(explode(',', $convocation->concour_times_ids));

        $data['convocation'] = $convocation;

        $data['concours_times'] = $concours_times;

        $pdf = PDF::loadView('convocation-pdf', $data);

        return $pdf->download('Convocation-UIR.pdf');
    }

    // Modifier mes Rdv de concours
    public function ModifierRdv(Request $request)
    {
        $this->validate($request, [
            'myrdv_time_id' => 'required|max:30',
            'api_token' => 'required|max:60',
        ]);

        // D'abord on annule les RDV réservés par le candidat
        $user = User::where('api_token', $request->api_token)->first();

        foreach ($user->concourtime as $old_rdv) {
            $old_rdv->update(['user_id' => null]);
        }

        // Mtnt on réserve les nouveaux RDV
        $myrdv_time_id = explode(',', $request->myrdv_time_id);

        foreach ($myrdv_time_id as $rdv) {
            ConcourTime::find($rdv)->update(['user_id' => $user->id]);
        }

        // Il faut mtnt annuler les anciennnes Convocations créées par l'utilisateur
        if ($user->convocation) {
            foreach ($user->convocation as $convocation) {
                $convocation->statut = 0;
                $convocation->save();
            }
        }

        // Enfin on crée la convocation et on lui affecte les éléments nécessaires
        $convocation = new Convocation();

        $convocation->user_id = $user->id;
        $convocation->concour_times_ids = $request->myrdv_time_id;
        $convocation->ref = 'UIR1920-0000'.$user->id;
        $convocation->statut = 1;
        $convocation->pdf_path = '/convocations/convocation-UIR'.$user->id.'-'.rand(1, 1000000).'.pdf';
        $convocation->save();

        return response()->json(['status' => 'success']);
    }
}
