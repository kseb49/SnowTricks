<?php

namespace App\Controller\Trait;

use Doctrine\Common\Collections\Collection;

trait CheckTrait
{


    /**
     * Look for a match in a collection
     *
     * @param Collection $collection
     * @param string     $subject
     * @param string     $param Name of the param
     * @return bool true on a match, false otherwise
     */
    private function check(Collection $collection, ?string $subject = null, string $param) :bool
    {
        if($subject !== null) {
            $param = 'get'.ucfirst($param);
            foreach ($collection as $src) {
                if ($src->$param() === $subject) {
                    return true;
                }
            }
        }
        return false;

    }


}
