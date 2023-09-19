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
    const IMAGES_MAX = 5;

    /**
     * The maximum of videos allowed
     */
    const VIDEOS_MAX = 5;

    /**
     * The maximum videos error message to display
     */
    const MAX_VIDEOS = ['max_reach' => 'videos'];

    /**
     * The maximum images error message to display
     */
    const MAX_IMAGES = ['max_reach' => 'images'];

    /**
     * The confirm key of the mail array
     */
    const CONFIRM = 'confirm';

    /**
     * The reset key
     */
    const RESET= 'reset';

    /**
     * The reset key
     */
    const DEFAULT= 'Erreur inconnue';

    /**
     * Set of variables needed for sending Emails
     *
     * @var array
     */
    private array $mail = [
        "confirm" => [
            "sujet" => "Confirmer votre compte",
            "route" => "account-confirmation",
            "template" => "emails/confirm.html.twig",
            "message" => "Un mail vous a été envoyé"
        ],
        "reset" => [
            "sujet" => "Réinitialisation de votre mot de passe",
            "route" => "password-reset",
            "template" => "emails/reset.html.twig",
            "message" => "Un mail vous a été envoyé"
        ]
    ];

    /**
     * Set of errors messages
     *
     * @var array
     */
    private array $errors = [
        "max_reach" => [
            "image" => "Le nombre maximum d'images est atteint pour cette figure",
            "videos" => "Le nombre maximum de vidéos est atteint pour cette figure"
     ]
    ];


    public function getMailParameters(string $confirm) :array
    {
        if (array_key_exists($confirm, $this->mail) === true) {
            return $this->mail[$confirm];
        }

    }


    public function getErrors(?array $max = null) :string
    {
        if($max !== null) {
            foreach ($max as $key => $value) {
                if (array_key_exists($key, $this->errors)) {
                    if (array_key_exists($value, $this->errors[$key])) {
                        return $this->errors[$key][$value];
                    }
                }
            }
        }
        return self::DEFAULT;

    }


}
