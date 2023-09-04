<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Figures;
use App\Form\ImageForm;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;






class ImageController extends AbstractController 
{

    // #[Route('/image/{id}',name:'image')]
    public function fetchImage(int $id)
    {
        $image= $entityManager->getRepository(Images::class)->find($id);
        dd($image);
        // $this->addFlash('warning', "Erreur");
        // return $this->render('edition/image_form.html.twig', [
        //     'image_form' => $form, 'figure' =>  $figure]);

    }
}