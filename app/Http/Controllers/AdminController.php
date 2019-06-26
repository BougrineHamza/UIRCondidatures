<?php

namespace App\Http\Controllers;

use App\City;
use App\Jury;
use App\User;
use App\Admin;
use App\Country;
use App\UirEcole;
use App\Specialite;
use App\ConcourDate;
use App\ConcourTime;
use App\Convocation;
use App\UirFormation;
use App\Mail\Decision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function AuthAdmin(Request $request)
    {
        return ['Authorized' => ($request->api_token && Admin::where('api_token', $request->api_token)->first())];
    }

    public function Connexion(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:80',
            'password' => 'required|max:60',
        ]);

        // On valide que c'est bien un e-mail UIR
        if (strpos($request->email, '@uir.ac.ma') === false) {
            return response()->json(['statut' => 'Ce n\'est pas un email UIR'], 400);
        }

        $admin = Admin::where('email', $request->email)->first();

        if ($admin) {
            // On contacte via Guzzle l'API de Microsoft Online pour avoir l'access_token
            $client = new \GuzzleHttp\Client();

            $res = $client->post(
                'https://login.microsoftonline.com/organizations/oauth2/v2.0/token',
                ['form_params' => ['grant_type' => 'password',
                        'username' => $request->email,
                        'password' => $request->password,
                        'client_id' => '00970931-21bf-46bc-9be9-f460d43fb6b7',
                        'scope' => 'openid User.Read',
                    ],
                ]);

            $access_token = json_decode($res->getBody())->access_token;

            if (strlen($access_token) > 20) {
                $admin->api_token = str_random(58);

                $admin->save();
            }

            return response()->json(['api_token' => $admin->api_token]);
        } else {
            return response()->json(['statut' => 'Cet utilisateur n\'est pas un administrateur']);
        }
    }

    // On importe le fichier excel dans la partie Entretiens par l'Admin
    public function apiImport(Request $request, \Maatwebsite\ExcelLight\Excel $excel, \Maatwebsite\ExcelLight\Reader $reader)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');

        $file_name = time().'-'.$file->getClientOriginalName();

        $file->move('import', $file_name);

        $reader = $excel->load('import/'.$file_name);

        $date_entretien = null;

        foreach ($reader->sheets() as $sheet) {
            foreach ($sheet->rows() as $key => $row) {
                if ($key != 1) {
                    $date_entretien = date('Y-m-d', $row->column('date_entretien')->getTimestamp());

                    $uir_formation = $row->column('formation_id');

                    $time = $row->column('heure_entretien');

                    $jury = $row->column('jury_id');

                    $bdd_date_entretien = ConcourDate::where([
                    'uirformation_id' => $uir_formation,
                    'date_concours' => $date_entretien, ])->first();

                    if (! $bdd_date_entretien) {
                        $nouveau_entretien_date = new ConcourDate();
                        $nouveau_entretien_date->uirformation_id = $uir_formation;
                        $nouveau_entretien_date->date_concours = $date_entretien;
                        $nouveau_entretien_date->save();

                        $bdd_date_entretien = $nouveau_entretien_date;
                    }

                    $entretien = ConcourTime::where([
                    'concour_date_id' => $bdd_date_entretien->id,
                    'uir_formation_id' => $uir_formation,
                    'time' => $time,
                ])->first();

                    if (! $entretien) {
                        $nouveau_entretien_time = new ConcourTime();
                        $nouveau_entretien_time->concour_date_id = $bdd_date_entretien->id;
                        $nouveau_entretien_time->jury_id = $jury;
                        $nouveau_entretien_time->uir_formation_id = $uir_formation;
                        $nouveau_entretien_time->time = $time;
                        $nouveau_entretien_time->save();
                    }
                }
            }
        }

        return response()->json(['resultat' => 'done']);
    }

    public function Auth(Request $request)
    {
        return view('gestion.auth');
    }

    public function Deconnexion(Request $request)
    {
        $user = Admin::where('api_token', $request->api_token)->first();

        if ($user) {
            $user->api_token = null;
        }

        return ['deconnexion' => $user->save() ? true : false];
    }

    public function EntretienUpdate(Request $request)
    {
        $this->validate($request, [
            'statut' => 'required|numeric|max:4',
            'id_entretien' => 'required|numeric',

        ]);

        $entretien = ConcourTime::find($request->id_entretien);

        if ($request->statut == 3) {
            $entretien->statut = null;
        } else {
            $entretien->statut = $request->statut;

            Mail::to($entretien->user)->send(new Decision($entretien->user->email, $entretien->user->password, $request->statut, $entretien->uirformation->titre, $entretien->uirformation->uirecole->titre, $entretien->user->first_name, $entretien->user->last_name));
        }

        $entretien->save();

        return response()->json(['success']);
    }

    public function Candidats(Request $request)
    {
        $admin = Admin::where('api_token', $request->api_token)->first();

        if ($admin->uirecole_id != 0) {
            $users = User::with('profil', 'cursus', 'convocation', 'concourtime.uirformation', 'stat')->whereHas('concourtime.uirformation.uirecole', function ($q) use ($admin) {
                $q->where('id', $admin->uirecole_id);
            })->orderBy('id', 'DESC')->get();
        } else {
            $users = User::with('profil', 'cursus', 'convocation', 'concourtime.uirformation', 'stat')->orderBy('id', 'DESC')->get();
        }

        $role = $admin->role;

        return view('gestion.main-gestion', compact('users', 'role'));
    }

    public function apiCandidats(Request $request)
    {
        $admin = Admin::where('api_token', $request->api_token)->first();

        if ($admin->uirecole_id != 0) {
            $admin_formations = UirFormation::where('uirecole_id', $admin->uirecole_id)->pluck('id')->toArray();

            $users = new \Illuminate\Support\Collection();

            foreach ($admin_formations as $id_formation) {
                $fetched_users = User::with('profil', 'cursus', 'convocation', 'concourtime.uirformation', 'stat')->whereHas('cursus', function ($q) use ($id_formation) {
                    $q->where('mes_formations_uir', 'like', '%'.$id_formation.'%');
                })->orderBy('id', 'DESC')->get();

                $users = $users->merge($fetched_users);
            }

            $users = $users->unique();

            $users = $users->values()->all();
        } else {
            $users = User::with('profil', 'cursus', 'convocation', 'concourtime.uirformation', 'stat')->orderBy('id', 'DESC')->get();
        }

        return $users;
    }

    ////////////// DEBUT VOIR CONVOCATION
    public function VoirConvocation(Request $request)
    {
        $convocation = Convocation::where('id', $request->id)->orderBy('id', 'DESC')->first();

        if ($convocation) {
            // On ramène les informations à afficher sur la Convocation
            $concours_times = ConcourTime::findMany(explode(',', $convocation->concour_times_ids));

            return view('convocation', compact(['convocation', 'concours_times']));
        } else {
            return 'ID Convocation inexistant';
        }
    }

    ////////////// FIN VOIR CONVOCATION

    ////////////// DEBUT ECOLES
    public function apiEcoles(Request $request)
    {
        $admin = Admin::where('api_token', $request->api_token)->first();

        $ecoles = UirEcole::orderBy('id', 'DESC')->get();

        return compact('ecoles');
    }

    public function EcolesDelete(Request $request)
    {
        $this->validate($request, [
        'id' => 'required',
    ]);

        $ecole = UirEcole::find($request->id);

        $ecole->delete();

        return response()->json(['statut' => 'success']);
    }

    public function EcolesAdd(Request $request)
    {
        $this->validate($request, [
            'titre' => 'required',
            'color' => 'required',
            'logo_path' => 'required',
        ]);

        $ecole = new UirEcole();

        $ecole->titre = $request->titre;
        $ecole->color = $request->color;
        $ecole->logo_path = $request->logo_path;

        $ecole->save();

        return response()->json(['statut' => 'success']);
    }

    public function Ecole(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $ecole = UirEcole::find($request->id);

        return response()->json(['ecole' => $ecole]);
    }

    public function EcolesUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'titre' => 'required',
            'color' => 'required',
            'logo_path' => 'required',
        ]);

        $ecole = UirEcole::find($request->id);

        $ecole->titre = $request->titre;
        $ecole->color = $request->color;
        $ecole->logo_path = $request->logo_path;

        $ecole->save();

        return response()->json(['statut' => 'success']);
    }

    ///// END ECOLES

    ///// DEBUT FORMATIONS
    public function apiFormations(Request $request)
    {
        $admin = Admin::where('api_token', $request->api_token)->first();

        if ($admin->uirecole_id != 0) {
            $formations = UirFormation::with('uirecole')->whereHas('uirecole', function ($q) use ($admin) {
                $q->where('id', $admin->uirecole_id);
            })->orderBy('id', 'DESC')->get();
        } else {
            $formations = UirFormation::with('uirecole')->orderBy('id', 'DESC')->get();
        }

        $ecoles = UirEcole::all();

        return compact('formations', 'ecoles');
    }

    public function FormationsDelete(Request $request)
    {
        $this->validate($request, [
        'id' => 'required',
    ]);

        $formation = UirFormation::find($request->id);

        $formation->delete();

        return response()->json(['statut' => 'success']);
    }

    public function FormationsAdd(Request $request)
    {
        $this->validate($request, [
            'titre' => 'required',
            'prerequis_html' => 'required',
            'condition_niveau' => 'required',
            'uirecole_id' => 'required',
        ]);

        $formation = new UirFormation();

        $formation->titre = $request->titre;
        $formation->prerequis_html = $request->prerequis_html;
        $formation->condition_niveau = $request->condition_niveau;
        $formation->uirecole_id = $request->uirecole_id;

        $formation->save();

        return response()->json(['statut' => 'success']);
    }

    public function Formation(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $formation = UirFormation::find($request->id);

        return response()->json(['formation' => $formation]);
    }

    public function FormationsUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'titre' => 'required',
            'prerequis_html' => 'required',
            'condition_niveau' => 'required',
            'uirecole_id' => 'required',
        ]);

        $formation = UirFormation::find($request->id);

        $formation->titre = $request->titre;
        $formation->prerequis_html = $request->prerequis_html;
        $formation->condition_niveau = $request->condition_niveau;
        $formation->uirecole_id = $request->uirecole_id;

        $formation->save();

        return response()->json(['statut' => 'success']);
    }

    ///// END FORMATIONS

    //////// DEBUT JURYS
    public function apiJurys(Request $request)
    {
        $admin = Admin::where('api_token', $request->api_token)->first();

        if ($admin->uirecole_id != 0) {
            $jurys = Jury::with('uirecole')->whereHas('uirecole', function ($q) use ($admin) {
                $q->where('id', $admin->uirecole_id);
            })->orderBy('id', 'DESC')->get();
        } else {
            $jurys = Jury::with('uirecole')->orderBy('id', 'DESC')->get();
        }

        $ecoles = UirEcole::all();

        $role = $admin->role;

        return compact('jurys', 'ecoles', 'role');
    }

    public function JurysDelete(Request $request)
    {
        $this->validate($request, [
        'id' => 'required',
    ]);

        $jury = Jury::find($request->id);

        $jury->delete();

        return response()->json(['statut' => 'success']);
    }

    public function JurysAdd(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'uirecole_id' => 'required',
        ]);

        $jury = new Jury();

        $jury->name = $request->name;
        $jury->email = $request->email;
        $jury->phone = $request->phone;
        $jury->uirecole_id = $request->uirecole_id;

        $jury->save();

        return response()->json(['statut' => 'success']);
    }

    public function Jury(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $jury = Jury::find($request->id);

        return response()->json(['jury' => $jury]);
    }

    public function JurysUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'uirecole_id' => 'required',
        ]);

        $jury = Jury::find($request->id);

        $jury->name = $request->name;
        $jury->email = $request->email;
        $jury->phone = $request->phone;
        $jury->uirecole_id = $request->uirecole_id;

        $jury->save();

        return response()->json(['statut' => 'success']);
    }

    /////// END JURYS

    ////// DEBUT ENTRETIENS
    public function apiEntretiens(Request $request)
    {
        $admin = Admin::where('api_token', $request->api_token)->first();

        if ($admin->uirecole_id == 0) {
            $entretiens = ConcourTime::with('user', 'user.profil', 'user.cursus', 'jury', 'concourdate', 'uirformation', 'uirformation.uirecole')->orderBy('id', 'DESC')->get();
        } else {
            $entretiens = ConcourTime::with('user', 'user.profil', 'user.cursus', 'jury', 'concourdate', 'uirformation', 'uirformation.uirecole')->whereHas('uirformation.uirecole', function ($q) use ($admin) {
                $q->where('id', $admin->uirecole_id);
            })->orderBy('id', 'DESC')->get();
        }

        $ecoles = UirEcole::all();

        return compact('entretiens', 'ecoles');
    }

    public function GetFormasJurys(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);

        $formations_list = UirFormation::where('uirecole_id', $request->id)->get();

        $jurys_list = Jury::where('uirecole_id', $request->id)->get();

        return response()->json(['formations_list' => $formations_list, 'jurys_list' => $jurys_list]);
    }

    public function EntretiensDelete(Request $request)
    {
        $this->validate($request, [
        'id' => 'required',
    ]);

        $entretien = ConcourTime::find($request->id);

        $entretien->delete();

        return response()->json(['statut' => 'success']);
    }

    public function EntretiensAdd(Request $request)
    {
        $this->validate($request, [
            'jury_id' => 'required',
            'uir_formation_id' => 'required',
            'time' => 'required',
            'date_concours' => 'required',
        ]);

        $date = ConcourDate::where(['date_concours' => $request->date_concours, 'uirformation_id' => $request->uir_formation_id])->first();

        if ($date) {
            $concour_date_id = $date->id;
        } else {
            $date = new ConcourDate();

            $date->date_concours = $request->date_concours;

            $date->uirformation_id = $request->uir_formation_id;

            $date->save();

            $concour_date_id = $date->id;
        }

        $entretien = new ConcourTime();

        $entretien->time = $request->time;

        $entretien->jury_id = $request->jury_id;

        $entretien->uir_formation_id = $request->uir_formation_id;

        $entretien->concour_date_id = $concour_date_id;

        $entretien->save();

        return response()->json(['statut' => 'success']);
    }

    public function Entretien(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);

        $entretien = ConcourTime::find($request->id);

        $date = ConcourDate::find($entretien->concour_date_id)->date_concours;

        $ecole_id = UirFormation::find($entretien->uir_formation_id)->uirecole_id;

        return response()->json(['entretien' => $entretien, 'date_concours' => $date, 'uirecole_id' => $ecole_id]);
    }

    public function EntretiensUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'jury_id' => 'required',
            'uir_formation_id' => 'required',
            'time' => 'required',
            'date_concours' => 'required',
        ]);

        $date = ConcourDate::where(['date_concours' => $request->date_concours, 'uirformation_id' => $request->uir_formation_id])->first();

        if ($date) {
            $concour_date_id = $date->id;
        } else {
            $date = new ConcourDate();

            $date->date_concours = $request->date_concours;

            $date->uirformation_id = $request->uir_formation_id;

            $date->save();

            $concour_date_id = $date->id;
        }

        $entretien = ConcourTime::find($request->id);

        $entretien->jury_id = $request->jury_id;

        $entretien->uir_formation_id = $request->uir_formation_id;

        $entretien->time = $request->time;

        $entretien->uir_formation_id = $request->uir_formation_id;

        $entretien->concour_date_id = $concour_date_id;

        $entretien->save();

        return response()->json(['statut' => 'success']);
    }

    ////// END ENTRETIENS

    ///// DEBUT SPECIALITES
    public function apiSpecialites(Request $request)
    {
        $admin = Admin::where('api_token', $request->api_token)->first();
        $specialites = Specialite::orderBy('id', 'DESC')->get();

        return compact('specialites');
    }

    public function SpecialitesDelete(Request $request)
    {
        $this->validate($request, [
        'id' => 'required',
    ]);

        $specialite = Specialite::find($request->id);

        $specialite->delete();

        return response()->json(['statut' => 'success']);
    }

    public function SpecialitesAdd(Request $request)
    {
        $this->validate($request, [
            'titre' => 'required',
        ]);

        $specialite = new Specialite();

        $specialite->titre = $request->titre;

        $specialite->save();

        return response()->json(['statut' => 'success']);
    }

    public function Specialite(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $specialite = Specialite::find($request->id);

        return response()->json(['specialite' => $specialite]);
    }

    public function SpecialitesUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'titre' => 'required',
        ]);

        $specialite = Specialite::find($request->id);

        $specialite->titre = $request->titre;

        $specialite->save();

        return response()->json(['statut' => 'success']);
    }

    ///// FIN SPECIALITES

    //// DEBUT VILLES/PAYS/NATIONALITES
    public function apiVilles(Request $request)
    {
        $admin = Admin::where('api_token', $request->api_token)->first();
        $villes = City::with('country')->get();
        $payss = Country::all();

        return compact('villes', 'payss');
    }

    public function Ville(Request $request)
    {
        $this->validate($request, [
        'id' => 'required',
    ]);

        $ville = City::find($request->id);

        return response()->json(['ville' => $ville]);
    }

    public function Country(Request $request)
    {
        $this->validate($request, [
        'id' => 'required',
    ]);

        $Country = Country::find($request->id);

        return response()->json(['Country' => $Country]);
    }

    public function VillesUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'country_id' => 'required',
            'titre' => 'required',
        ]);

        $ville = City::find($request->id);

        $ville->country_id = $request->country_id;

        $ville->titre = $request->titre;

        $ville->save();

        response()->json(['statut' => 'enregistré']);
    }

    public function VillesAdd(Request $request)
    {
        $this->validate($request, [
        'country_id' => 'required',
        'titre' => 'required',
    ]);

        $ville = new City();

        $ville->country_id = $request->country_id;

        $ville->titre = $request->titre;

        $ville->save();

        response()->json(['statut' => 'enregistré']);
    }

    public function VillesDelete(Request $request)
    {
        $this->validate($request, [
        'id' => 'required',
    ]);

        $ville = City::find($request->id)->delete();

        response()->json(['statut' => 'enregistré']);
    }

    //// - Pays -

    public function PaysUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'titre' => 'required',
            'nationalite' => 'required',
        ]);

        $Country = Country::find($request->id);

        $Country->titre = $request->titre;

        $Country->nationalite = $request->nationalite;

        $Country->save();

        response()->json(['statut' => 'enregistré']);
    }

    public function PaysAdd(Request $request)
    {
        $this->validate($request, [
        'titre' => 'required',
        'nationalite' => 'required',
    ]);

        $Country = new Country();

        $Country->titre = $request->titre;

        $Country->nationalite = $request->nationalite;

        $Country->save();

        response()->json(['statut' => 'enregistré']);
    }

    public function PaysDelete(Request $request)
    {
        $this->validate($request, [
        'id' => 'required',
    ]);

        $Country = Country::find($request->id)->delete();

        response()->json(['statut' => 'enregistré']);
    }

    //// FIN VILLES/PAYS/NATIONALITES

    ///// DEBUT USERS
    public function apiUsers(Request $request)
    {
        $admin = Admin::where('api_token', $request->api_token)->first();
        $admins = Admin::with('uirecole')->orderBy('id', 'DESC')->get();
        $ecoles = UirEcole::all();

        return compact('admins', 'ecoles');
    }

    public function UsersDelete(Request $request)
    {
        $this->validate($request, [
        'id' => 'required',
    ]);

        $admin = Admin::find($request->id);

        $admin->delete();

        return response()->json(['statut' => 'success']);
    }

    public function UsersAdd(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'uirecole_id' => 'required',
        ]);

        $admin = new Admin();

        $admin->first_name = $request->first_name;
        $admin->last_name = $request->last_name;
        $admin->email = $request->email;
        $admin->uirecole_id = $request->uirecole_id;

        $admin->save();

        return response()->json(['statut' => 'success']);
    }

    public function User(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $candidat = User::where('id', $request->id)->with('profil', 'cursus', 'stat', 'convocation', 'concourtime', 'concourtime.uirformation', 'concourtime.uirformation.uirecole', 'concourtime.concourdate')->first();

        if ($candidat->cursus) {
            $array = explode(',', $candidat->cursus->mes_formations_uir);

            $formations = UirFormation::whereIn('id', $array)->get();
        } else {
            $formations = null;
        }

        return response()->json(['candidat' => $candidat, 'formations_choisies' => $formations]);
    }

    public function UserToken(Request $request)
    {
        $admin = Admin::where('api_token', $request->api_token)->with('uirecole')->first();

        return response()->json(['admin' => $admin]);
    }

    public function UsersUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'uirecole_id' => 'required',
        ]);

        $admin = Admin::find($request->id);

        $admin->first_name = $request->first_name;
        $admin->last_name = $request->last_name;
        $admin->email = $request->email;
        $admin->uirecole_id = $request->uirecole_id;

        $admin->save();

        return response()->json(['statut' => 'success']);
    }

    //// FIN USERS
}
