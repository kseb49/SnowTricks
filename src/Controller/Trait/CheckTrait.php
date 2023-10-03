<?php 

namespace App\Controller\Trait;

use Doctrine\Common\Collections\Collection;

trait CheckTrait
{

    /**
     * Look for a match in a collection
     *
     * @param Collection $collection
     * @param string $subject 
     * @param string $param Name of the param
     * @return bool True on success, false on failure
     */
    private function check(Collection $collection, string $subject, string $param) :bool
    {
        $param = 'get'.ucfirst($param);
        foreach ($collection as $src) {
            if($src->$param() === $subject) {
               return true;
            }
        }
    }
}
