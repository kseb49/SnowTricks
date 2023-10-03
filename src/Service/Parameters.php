<?php 

namespace App\Service;

class Parameters
{

    /**
     * The default trick image
     * @var string
     */
    const DEFAULT_IMAGE = "snow_board.jpeg";
    
    /**
     * The user password used in fixtures
     * @var string
     */
    const DEFAULT_PASSWORD = "123456";

    /**
     * The maximum of images allowed
     * @var int
     */
    const MAX_IMAGES = 5;
    
    /**
     * The maximum of videos allowed
     * @var int
     */
    const MAX_VIDEOS = 5;
    
    /**
     * The from adress for the email
     * @var string
     */
    const FROM = 'Sébastien <snowtricks@example.com>';
    
    /**
     * The default error message
     * @var string
     */
    const DEFAULT = "Erreur inconnue";
    
    /**
     * The reset key
     * @var string
     */
    const RESET = "reset";
    
    /**
     * The confirm key
     * @var string
     */
    const CONFIRM = "confirm";
    
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
        "image" => " ⚠️Le nombre maximum d'images est atteint pour cette figure",
        "videos" => " ⚠️Le nombre maximum de vidéos est atteint pour cette figure"
        ],
    "link" => [
        "expired" => " ⚠️Ce lien n\'est pas valable. Un nouveau vous a été envoyé à votre adresse mail",
        "invalid" => "⚠️ Ce lien n'est pas valable"
        ],
    "videos" => [
        "used" => "⚠️ Cette vidéo est déjà utilisée dans cette figure"
        ],
    "unknown" => [
        "figure" => "⚠️ Cette figure n'existe pas",
        "video" => "⚠️ Cette vidéo n'existe pas",
        "image" => "⚠️ Cette image n'existe pas",
        ],
    "authenticate" => [
        "wrong" => "⚠️ Déconnectez vous pour accéder à cette page",
        "access" => "⚠️ Vous ne pouvez pas exécuter cette action",
        ]
    ];
    
    /**
     * Set of Feedback messages
     *
     * @var array
     */
    private array $feedback= [
        "delete" => [
            "message" => "Suppression réussit 😉"
            ],
        "edit" => [
            "message" =>  "Modifé avec succès 😉",
            "missing" =>  "Vous n'avez rien envoyé ⚠️",
            ],
        "only" => [
            "image" => "Cette image ne peut pas être supprimé car c'est la seule pour ce trick ⚠️"
            ],
        "user" => [
            "confirm" => 'Votre compte est confirmé 😉',
            "ever" => '⚠️ Votre compte est déjà confirmé ⚠️',
            "before" => '⚠️ Votre compte doit être confirmé avant ⚠️',
            "unknown" => "⛔ Il n'y a pas de compte associé à ce nom",
            ],
        "success" => [
            "image" => "L'image est en ligne 😉",
            "videos" => "La vidéo est en ligne 😉",
            "figure" => "La figure est en ligne 😉",
            "comment" => "Votre commentaire est en ligne 😊",
            "password" => "Votre nouveau mot de passe est opérationnel 😉"
            ]
        ];


    
    public function getMailParameters(string $confirm) :array
    {
        if (array_key_exists($confirm, $this->mail) === true) {
            return $this->mail[$confirm];
        }
        
    }

    
    public function getMessages(string $subject, ?array $max = null) :string
    {
        if ($max !== null) {
            foreach ($max as $key => $value) {
                if (array_key_exists($key, $this->$subject)) {
                    if (array_key_exists($value, $this->$subject[$key])) {
                        return $this->$subject[$key][$value];
                    }

                }

            }
        }

        return self::DEFAULT;

    }


}
