<?php 

namespace App\Service;

class Parameters
{
    /**
     * The maximum videos error message to display
     */
    public array $max_videos = ['max_reach' => 'videos'];

    /**
     * The maximum images error message to display
     */
    public array $max_images = ['max_reach' => 'images'];

    /**
     * The maximum images error message to display
     */
    const EXPIRED = ['expired_link' => 'message'];

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
        ],
        "link" => [
            "expired" => "Ce lien n\'est pas valable. Un nouveau vous a été envoyé à votre adresse mail",
            "invalid" => "Ce lien n'est pas valable"
        ],
        "unknown" => [
            "message" => "Cette figure n'existe pas"
            ]
    ];

    /**
     * Set of Feedback messages
     *
     * @var array
     */
    private array $feedback= [
        "delete" => [
            "message" => "Suppression réussit 😊"
        ],
        "edit" => [
            "message" =>  "Modifé avec succès 😊"
        ],
        "only" => [
            "image" => "Cette image ne peut pas être supprimé car c'est la seule pour ce trick"
        ],
        "user" => [
            "confirm" => 'Votre compte est confirmé',
            "ever" => 'Votre compte est déjà confirmé'
        ],
        "success" => [
            "image" => "L'image est en ligne",
            "figure" => "La figure est en ligne",
            "comment" => "Votre commentaire est en ligne 😊",
            "password" => "Votre nouveau mot de passe est opérationnel"
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
        if($max !== null) {
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
