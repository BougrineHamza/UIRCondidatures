<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlerteJury extends Mailable
{
    use Queueable, SerializesModels;

    public $formation;

    public $ecole;

    public $date_entretien;

    public $heure_entretien;

    public $nom_candidat;

    public $jury_name;

    public $cin_candidat;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($formation, $ecole, $date_entretien, $heure_entretien, $nom_candidat, $jury_name, $cin_candidat)
    {
        $this->formation = $formation;

        $this->ecole = $ecole;

        $this->date_entretien = $date_entretien;

        $this->heure_entretien = $heure_entretien;

        $this->nom_candidat = $nom_candidat;

        $this->jury_name = $jury_name;

        $this->cin_candidat = $cin_candidat;
    }

    /**
     * Build the message.
     * @return $this
     */
    public function build()
    {
        return $this->from('candidatures@uir.ac.ma')
                    ->subject('Entretien programmé')
                    ->markdown('email.jury_entretien')
                    ->with([
                        'formation' => $this->formation,
                        'ecole' => $this->ecole,
                        'date_entretien' => $this->date_entretien,
                        'heure_entretien' => $this->heure_entretien,
                        'nom_candidat' => $this->nom_candidat,
                        'jury_name' => $this->jury_name,
                        'cin_candidat' => $this->cin_candidat, ]);
    }
}
