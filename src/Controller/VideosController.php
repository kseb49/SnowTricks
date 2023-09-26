<?php

namespace App\Controller;

use App\Entity\Videos;
use App\Entity\Figures;
use App\Form\VideoForm;
use App\Form\AddVideoForm;
use App\Service\Parameters;
use App\Repository\VideosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VideosController extends AbstractController
{


    #[Route('/ajout-video/{id}', name:'add_video')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    /**
     * Add a video to a trick
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param integer $id The trick id
     * @param VideosRepository $videoRepo
     * @param Parameters $parameters
     * @return Response
     */
    public function addVideo(Request $request, EntityManagerInterface $entityManager, int $id,VideosRepository $videoRepo, Parameters $parameters) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $form = $this->createForm(AddVideoForm::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
                $videos = $form->get('videos')->getData();
                if ($videos) {
                    foreach ($videos as $value) {
                        // The maximum number of videos allowed.
                        if ($videoRepo->countVideos($id)[1] >= $_ENV['VIDEOS_MAX']) {
                            $this->addFlash('warning', $parameters->getMessages('error', ['max_reach' => 'videos']));
                            return $this->redirectToRoute('figuresdetails', ["slug" => $figure->getSlug()]);
                        }
                        if (count($figure->getVideos()) > 0) {
                            foreach ($figure->getVideos() as $src) {
                                if($src->getSrc() === $value->getSrc()) {
                                    $this->addFlash('danger', $parameters->getMessages('error', ['videos' => 'used']));
                                    return $this->redirectToRoute('figuresdetails', ["slug" => $figure->getSlug()]);
                                }
                            }
                        }
                        $embed = new Videos;
                        $embed->setSrc($value->getSrc());
                        $figure->addVideos($embed);
                        $entityManager->persist($figure);
                        $entityManager->flush();
                    }

                    $this->addFlash('success', $parameters->getMessages('feedback', ['success' => 'videos']));
                    return $this->redirectToRoute('figuresdetails', ["slug" => $figure->getSlug()]);

                }
        }

        return $this->render('edition/add_video_form.html.twig', ['form' => $form, 'figure' => $figure]);

    }


    #[Route('/modification-video/{id}/{video_id}',name:'edit_video')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    /**
     * Edit a video from a trick
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param integer $id
     * @param integer $video_id
     * @return Response
     */
    public function editVideo(Request $request, EntityManagerInterface $entityManager, int $id, int $video_id, Parameters $parameters) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $video = $entityManager->getRepository(Videos::class)->find($video_id);
        $form = $this->createForm(VideoForm::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            if (count($figure->getVideos()) > 0) {
                foreach ($figure->getVideos() as $src) {
                    if($src->getSrc() === $form->get('src')->getData() && $src->getId() !== $video->getId()) {
                        $this->addFlash('danger', $parameters->getMessages('error', ['videos' => 'used']));
                        return $this->redirectToRoute('figuresdetails', ["slug" => $figure->getSlug()]);
                    }
                }
            }
            $video->setSrc($form->get('src')->getData());
            $entityManager->persist($video);
            $entityManager->flush();
            $this->addFlash('success','modifé avec succès');
            return $this->redirectToRoute('figuresdetails', ['slug' => $figure->getSlug()]);
        }

        return $this->render('edition/edit_video.html.twig', ['form' => $form, 'figure' => $figure]);

    }


    #[Route('/suppression-video/{id}/{video_id}',name:'delete_video')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    public function deleteVideo(EntityManagerInterface $entityManager, int $id , int $video_id, Parameters $parameters) :Response
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
        $this->addFlash('success', $parameters->getMessages('feedback', ['delete' => 'message']));
        return $this->redirectToRoute('home');

    }


}