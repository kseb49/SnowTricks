<?php

namespace App\Controller;

use App\Controller\Trait\CheckTrait;
use App\Entity\Videos;
use App\Entity\Figures;
use App\Form\VideoForm;
use App\Form\AddVideoForm;
use App\Service\Parameters;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VideosController extends AbstractController
{

    use CheckTrait;


    public function __construct(public Parameters $parameters)
    {

    }


    /**
     * Add a video to a trick
     *
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param integer                $id The trick id
     * @param VideosRepository       $videoRepo
     * @param Parameters             $parameters
     * @return Response
     */
    #[Route('/ajout-video/{id}', name:'add_video')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    public function addVideo(Request $request, EntityManagerInterface $entityManager, int $id) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        // Avoid exceeding the maximum number of videos allowed.
        if (count($figure->getVideos()) >= $this->getParameter('VIDEOS_MAX')) {
            $this->addFlash('warning', $this->parameters->getMessages('errors', ['max_reach' => 'videos']));
            return $this->redirectToRoute('figuresdetails', ["slug" => $figure->getSlug()]);
        }

        $form = $this->createForm(AddVideoForm::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $videos = $form->get('videos')->getData();
            if ($videos) {
                foreach ($videos as $value) {
                        // The maximum number of videos allowed.
                        if (count($figure->getVideos()) >= $this->getParameter('VIDEOS_MAX')) {
                            $this->addFlash('warning', $this->parameters->getMessages('errors', ['max_reach' => 'videos']));
                            return $this->redirectToRoute('figuresdetails', ["slug" => $figure->getSlug()]);
                        }

                        // Avoid duplicate videos.
                        if (count($figure->getVideos()) > 0) {
                            if ($this->check($figure->getVideos(), $value->getSrc(), 'src') === true) {
                                $this->addFlash('danger', $this->parameters->getMessages('errors', ['videos' => 'used']));
                                return $this->redirectToRoute('figuresdetails', ["slug" => $figure->getSlug()]);
                            }
                        }
                        $embed = new Videos;
                        $embed->setSrc($value->getSrc());
                        $figure->addVideos($embed);
                        $entityManager->persist($figure);
                        $entityManager->flush();
                }
                $this->addFlash('success', $this->parameters->getMessages('feedback', ['success' => 'videos']));
                return $this->redirectToRoute('home');
            }
            $this->addFlash('danger', $this->parameters->getMessages('feedback', ['edit' => 'missing']));
            return $this->render('edition/add_video_form.html.twig', ['form' => $form, 'figure' => $figure]);
        }
        return $this->render('edition/add_video_form.html.twig', ['form' => $form, 'figure' => $figure]);

    }


    /**
     * Edit a video from a trick
     *
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param integer                $id The trick id
     * @param integer                $video_id The video id
     * @return Response
     */
    #[Route('/modification-video/{id}/{video_id}', name:'edit_video')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    public function editVideo(Request $request, EntityManagerInterface $entityManager, int $id, int $video_id) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $video = $entityManager->getRepository(Videos::class)->find($video_id);
        $form = $this->createForm(VideoForm::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            if (count($figure->getVideos()) > 0) {
                foreach ($figure->getVideos() as $src) {
                     // Avoid duplicate
                    if ($src->getSrc() === $form->get('src')->getData() && $src->getId() !== $video->getId()) {
                        $this->addFlash('danger', $this->parameters->getMessages('errors', ['videos' => 'used']));
                        return $this->redirectToRoute('figuresdetails', ["slug" => $figure->getSlug()]);
                    }

                }
            }
            if ($form->get('src')->getData() !== null) {
                $video->setSrc($form->get('src')->getData());
                $entityManager->persist($video);
                $entityManager->flush();
                $this->addFlash('success',$this->parameters->getMessages('feedback', ['edit' => 'message']));
                return $this->redirectToRoute('home');
            }
            $this->addFlash('danger', $this->parameters->getMessages('feedback', ['edit' => 'missing']));
            return $this->render('edition/edit_video.html.twig', ['form' => $form, 'figure' => $figure]);
        }

        return $this->render('edition/edit_video.html.twig', ['form' => $form, 'figure' => $figure]);

    }



    #[Route('/suppression-video/{id}/{video_id}',name:'delete_video')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    /**
     * Delete a video from a trick
     *
     * @param EntityManagerInterface $entityManager
     * @param integer                $id The trick id
     * @param integer                $video_id The video id
     * @param Request                $request
     * @return Response
     */
    public function deleteVideo(EntityManagerInterface $entityManager, int $id, int $video_id, Request $request) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $videos = $entityManager->getRepository(Videos::class)->find($video_id);
        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
            if (!$videos) {
                $this->addFlash('danger', ['unknown' => 'video']);
                return $this->redirectToRoute('figuresdetails', ['slug' => $figure->getSlug()]);
            }

            $figure->removeVideos($videos);
            $entityManager->remove($videos);
            $entityManager->persist($figure);
            $entityManager->flush();
            $this->addFlash('success', $this->parameters->getMessages('feedback', ['delete' => 'message']));
            return $this->redirectToRoute('home');
        }

        $this->addFlash('warning', $this->parameters->getMessages('errors', ['authenticate' => 'access']));
        return $this->redirectToRoute('figuresdetails', ['slug' => $figure->getSlug()]);

    }


}
