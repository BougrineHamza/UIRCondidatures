<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Decision extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    public $password;

    public $resultat;

    public $titre_entretien;

    public $ecole;

    public $prenom;

    public $nom;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email,$password, $resultat, $titre_entretien, $ecole, $prenom, $nom)
    {
        $this->email = $email;

        $this->password = $password;

        $this->resultat = $resultat;

        $this->titre_entretien = $titre_entretien;

        $this->ecole = $ecole;

        $this->prenom = $prenom;

        $this->nom = $nom;



    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
        return $this->from('candidatures@uir.ac.ma')
                        ->subject('Notification d\'admission au Master')
                        ->markdown('email.resultat_concours_rbs')
                        ->with(['email' => $this->email, 'password' => $this->password, 'resultat' => $this->resultat, 'titre_entretien' => $this->titre_entretien, 'ecole' => $this->ecole,'prenom' => $this->prenom, 'nom' => $this->nom]);
        // else {
        //     return $this->from('candidatures@uir.ac.ma')
        //                 ->subject('Résultat Entretien')
        //                 ->markdown('email.resultat_concours')
        //                 ->with(['email' => $this->email, 'password' => $this->password, 'resultat' => $this->resultat, 'titre_entretien' => $this->titre_entretien, 'ecole' => $this->ecole,'prenom' => $this->prenom, 'nom' => $this->nom]);
        // }
    }
}
