<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConvocationCreee extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email,$password)
    {
        $this->email = $email;

        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('candidatures@uir.ac.ma')
                    ->subject('Convocation créée avec succès')
                    ->markdown('email.convocation-creee')
                    ->with(['email' => $this->email, 'password' => $this->password]);
    }
}
