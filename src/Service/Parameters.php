<?php 

namespace App\Service;

class Parameters
{
    /**
     * The default figure image 
     */
    const DEFAULT_IMG = "snow_board.jpeg";

    /**
     * The maximum of images allowed
     */
    const MAX = 5;

    /**
     * The confirm key of the mail array
     */
    const CONFIRM = 'confirm';

    /**
     * The reset key
     */
    const RESET= 'reset';

    /**
     * Set of variables needed for sending Emails
     *
     * @var array
     */
    private array $mail = [
        "confirm" => [
            "sujet" => "Confirmer votre compte",
            "route" => "account_confirmation",
            "template" => "emails/confirm.html.twig"
        ],
        "reset" => [
            "sujet" => "Réinitialisation de votre mot de passe",
            "route" => "password_reset",
            "template" => "emails/confirm.html.twig"
        ]
    ];


    public function getMailParameters(string $confirm) :array
    {
        if (array_key_exists($confirm, $this->mail) === true) {
            return $this->mail[$confirm];
        }

    }


}
