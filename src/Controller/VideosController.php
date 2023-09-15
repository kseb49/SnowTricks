<?php

namespace App\Controller;

use App\Entity\Videos;
use App\Entity\Figures;
use App\Form\VideoForm;
use App\Form\AddVideoForm;
use App\Repository\VideosRepository;
use App\Service\Parameters;
use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VideosController extends AbstractController
{


    #[Route('/ajout-video/{id}',name:'add_video')]
    public function addVideo(Request $request, EntityManagerInterface $entityManager, int $id,VideosRepository $videoRepo,Parameters $parameters) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $form = $this->createForm(AddVideoForm::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
                $videos = $form->get('videos')->getData();
                if ($videos) {
                    foreach ($videos as $value) {
                        if ($videoRepo->countVideos($id)[1] >= $parameters::MAX) {
                            $this->addFlash('warning',"Le nombre maximum de vidéos est atteint");
                            return $this->redirectToRoute('figuresdetails',["slug" => $figure->getSlug()]);
                        }
                            $embed = new Videos;
                            $embed->setSrc($value->getSrc());
                            $figure->addVideos($embed);
                            $entityManager->persist($figure);
                            $entityManager->flush();
                    }
                    $this->addFlash('success', "La vidéo est en ligne 😊");
                    return $this->redirectToRoute('home');
                 }

        }

        return $this->render('edition/add_video_form.html.twig', ['form' => $form, 'figure' => $figure]);

    }


    #[Route('/modification-video/{id}/{video_id}',name:'edit_video')]
    public function editVideo(Request $request, EntityManagerInterface $entityManager, int $id, int $video_id) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $video = $entityManager->getRepository(Videos::class)->find($video_id);
        $form = $this->createForm(VideoForm::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $video->setSrc($form->get('src')->getData());
            $entityManager->persist($video);
            $entityManager->flush();
            $this->addFlash('success','modifé avec succès');
            return $this->redirectToRoute('figuresdetails',['slug' => $figure->getSlug()]);

        }

        return $this->render('edition/edit_video.html.twig', ['form' => $form, 'figure' => $figure]);

    }


    #[Route('/suppression-video/{id}/{video_id}',name:'delete_video')]
    public function deleteVideo(EntityManagerInterface $entityManager, int $id , int $video_id) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $videos = $entityManager->getRepository(Videos::class)->find($video_id);
        if(!$videos){
            $this->addFlash('danger', "Cette vidéo n'existe pas");
            return $this->redirectToRoute('home');
        }
        $figure->removeVideos($videos);
        $entityManager->remove($videos);
        $entityManager->persist($figure);
        $entityManager->flush();
        $this->addFlash('success', "Suppression réussit 😊");
        return $this->redirectToRoute('home');

    }


}