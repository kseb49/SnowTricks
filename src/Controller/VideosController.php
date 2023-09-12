<?php

namespace App\Controller;

use App\Entity\Videos;
use App\Entity\Figures;
use App\Form\VideoForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VideosController extends AbstractController
{


    #[Route('/modification-video/{id}/{video_id}',name:'edit_video')]
    public function editVideo(Request $request, EntityManagerInterface $entityManager, int $id, int $video_id) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $form = $this->createForm(VideoForm::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {

        }

        return $this->render('edition/add_video_form.html.twig', ['add_video_form' => $form, 'figure' => $figure]);

    }


}