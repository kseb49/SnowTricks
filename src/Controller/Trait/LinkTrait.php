<?php

namespace App\Controller\Trait;

use DateTime;
use App\Entity\Users;

trait LinkTrait
{


    /**
     * Create and set a token ready to persist
     * Token used to account confirmation or password reset
     * The creation date is also managed
     *
     * @param Users $user
     * @param string|null $algo Name of selected hashing algorithm (https://www.php.net/manual/fr/function.hash.php)
     * @return string The token created
     */
    public function setLink(Users $user, ?string $algo= 'md5') :string
    {

        $token = hash($algo, uniqid(true));
        $user->setToken($token);
        $user->setSendLink(new DateTime());
        return $token;

    }


}
