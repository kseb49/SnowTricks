<?php

namespace App\Service;


use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;


class ImageManager
{

    public function __construct(
        private ParameterBagInterface $targetDirectory,
        private SluggerInterface $slugger,
    ) 
    {
    }

    /**
     * Manage the incoming image - rename and store
     *
     * @param UploadedFile $image
     * @param string $targetDir
     * @return string The name of the image
     */
    public function upload(UploadedFile $image, string $targetDir) :string
    {

        $image_name = preg_replace("#[0-9]#", "", pathinfo($image->getClientOriginalName(),PATHINFO_FILENAME));
        $safeFilename = $this->slugger->slug($image_name);
        $new_photo_name =  $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
        $image->move($this->getTargetDirectory()->get($targetDir),$new_photo_name);
        return $new_photo_name;

    }

    /**
     * Delete a file
     *
     * @param string $targetDir Services.yaml parameters
     * @param string $path Path of the file from $targetDir
     * @return boolean
     */
    public function delete(string $targetDir, string $path) :bool
    {

        if (unlink($this->getTargetDirectory()->get($targetDir).$path)) {
            return true;
        }
        throw new \Exception("Fichier non supprimé");

    }


    public function getTargetDirectory() :ParameterBagInterface
    {
        return $this->targetDirectory;

    }


}