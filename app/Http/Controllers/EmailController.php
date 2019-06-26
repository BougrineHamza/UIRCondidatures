<?php

namespace App\Http\Controllers;

use App\Stat;
use App\User;
use App\Mail\AlerteJury;
use App\Mail\NouveauMDP; // Pour Hasher le mot de passe
// Définitions des emails à envoyer
use App\Mail\VerifierEmail;
use Illuminate\Http\Request;
use App\Mail\ConvocationCreee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    // FONCTIONS OUTBOUND MAIL
    // Envoyer e-mail Créer un nouveau mot de passe
    public function NouveauMDP(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        $user = User::where(['email' => $request->email])->firstOrFail();

        if ($user) {
            Mail::to($user)->send(new NouveauMDP($user->email, $user->password));

            return response()->json(['statut' => 'succes'], 200);
        } else {
            return response()->json(['statut' => 'E-mail inexistant'], 400);
        }
    }

    // Envoyer e-mail Envoyer e-mail de confirmation
    public function EnvoyerEmailConfirmation(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        $user = User::where(['email' => $request->email])->firstOrFail();

        if ($user) {
            Mail::to($user)->send(new VerifierEmail($user->email, $user->password));

            return response()->json(['statut' => 'succes'], 200);
        } else {
            return response()->json(['statut' => 'E-mail inexistatnt'], 400);
        }
    }

    // Envoyer e-mail Convocation créée
    public function ConvocationCreee(Request $request)
    {
        $this->validate($request, [
            'api_token' => 'required|max:60',
        ]);

        $user = User::where(['api_token' => $request->api_token])->firstOrFail();

        if ($user) {
            Mail::to($user)->send(new ConvocationCreee($user->email, $user->password));

            foreach ($user->concourtime as $entretien) {
                Mail::to($entretien->jury->email)->send(new AlerteJury($entretien->uirformation->titre, $entretien->uirformation->uirecole->titre, $entretien->concourdate->date_concours, $entretien->time, $entretien->user->first_name.' '.$entretien->user->last_name, $entretien->jury->name, $entretien->user->profil->cin));
            }

            return response()->json(['statut' => 'succes'], 200);
        } else {
            return response()->json(['statut' => 'Utilisateur inexistant'], 400);
        }
    }

    // FONCTIONS INBOUND MAIL
    // Traiter la requête e-mail : Accéder à la page Créer nouveau Mot de Passe
    public function PageNouveauMDP(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:80',
            'token' => 'required|max:60',
        ]);

        $user = User::where(['email' => $request->email, 'password' => $request->token])->first();

        // Si utilisateur existe, alors on l'authentifie pui on le redirige vers la page de téléchargement
        if ($user) {

            // Si l'utilisateur n'a pas une session ouverte, on lui crée une nouvelle session
            if (! $user->api_token) {
                $user->update(['api_token' => str_random(60)]);
            }

            if (! $user->email_confirmed) {
                // On enregistre que l'utilisateur a confirmé son email
                $user->email_confirmed = 1;

                $user->save();
            }

            // On connecte l'utilisateur via un middleware JS et on le redirige vers la page demandée
            return view('middleware')->with(['api_token' => $user->api_token, 'redirect' => '/parametres']);
        } else {
            return redirect('/');
        }
    }

    // Traiter la requête e-mail : Confirmer mon e-mail
    public function PageConfirmerEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:80',
            'token' => 'required|max:60',
        ]);

        $user = User::where(['email' => $request->email, 'password' => $request->token])->first();

        // Si utilisateur existe, alors on l'authentifie pui on le redirige vers la page de téléchargement
        if ($user) {

            // Si l'utilisateur n'a pas une session ouverte, on lui crée une nouvelle session
            if (! $user->api_token) {
                $user->update(['api_token' => str_random(60)]);
            }

            if (! $user->email_confirmed) {
                // On enregistre que l'utilisateur a confirmé son email
                $user->email_confirmed = 1;

                $user->save();
            }

            // On connecte l'utilisateur via un middleware JS et on le redirige vers la page Etape1
            return view('middleware')->with(['api_token' => $user->api_token, 'redirect' => '/etape1']);
        } else {
            return redirect('/');
        }
    }

    // Traiter la requête e-mail : Accéder ò la page de téléchargement de ma convocation
    public function PageConvocationCreee(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:80',
            'token' => 'required|max:61',
        ]);

        if (strlen($request->token) != 61) {
            header('Location: http://candidature.uirservices.ma');
            die();
        } else {
            $request->token = substr($request->token, 0, -1);

            $user = User::where(['email' => $request->email, 'password' => $request->token])->first();

            // Si utilisateur existe, alors on l'authentifie pui on le redirige vers la page de téléchargement
            if ($user) {

            // Si l'utilisateur n'a pas une session ouverte, on lui crée une nouvelle session
                if (! $user->api_token) {
                    $user->update(['api_token' => str_random(60)]);
                }

                if (! $user->email_confirmed) {
                    // On enregistre que l'utilisateur a confirmé son email
                    $user->email_confirmed = 1;

                    $user->save();
                }

                // On connecte l'utilisateur via un middleware JS et on le redirige vers la page demandée
                return view('middleware')->with(['api_token' => $user->api_token, 'redirect' => '/mes-convocations']);
            } else {
                return redirect('/');
            }
        }
    }
}
