<?php

namespace App\Controller;

use App\Entity\Figures;
use App\Entity\Images;
use App\Entity\Videos;
use App\Form\FigureForm;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/figures',name:'figures')]
class FigureController extends AbstractController 
{
    
    /**
     * The default image
     */
    const DEFAULT_IMG = "snow_board.jpeg";

    
    #[Route('/{slug}', name:'details')]
    /**
     * Page of a single trick
     *
     * @param Figures $figures 
     * @return Response
     */
    public function details(Figures $figures) :Response
    {
        if (!$figures) {
            $this->addFlash('danger', "Cette figure n'existe pas");
            return $this->redirectToRoute('home');
        }
        return $this->render('details.html.twig', [
            'figures' => $figures]);
    }


    #[Route('/creation-figure', name:'create', priority: 1)]
    /**
     * Create a trick page
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ImageUploader $upload
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $entityManager,ImageUploader $upload, SluggerInterface $slugger) :Response
    {
        $figure = new Figures();
        $form = $this->createForm(FigureForm::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('images')->getData();
            if ($image) {
                foreach ($image as $value) {
                    try {
                        $images_name = $upload->upload($value,'figures_directory');
                        $picture = new Images;
                        $picture->setImageName($images_name);
                        $figure->addImage($picture);
                    } catch (FileException $e) {
                        return $this->redirectToRoute('creation',["error" => $e]);
                    }
                }
            }
            else {
                $picture = new Images;
                $picture->setImageName(self::DEFAULT_IMG);
                $figure->addImage($picture);
            }
                $figure->setName($form->get('name')->getData());
                $figure->setSlug(strtolower($slugger->slug($form->get('name')->getData())));
                $figure->setDescription($form->get('description')->getData());
                $figure->setCreationDate();
                $figure->setUsersId($this->getUser());
                $figure->setGroupsId($form->get('groups_id')->getData());
                $videos = new Videos;
                $videos->setSrc($form->get('videos')->getData());
                $figure->addVideos($videos);
                $entityManager->persist($figure);
                $entityManager->flush();
                $this->addFlash('success', "La figure est en ligne 😊");
                return $this->redirectToRoute('home');

            }

        return $this->render('edition/new_figure.html.twig', [
            'figure_form' => $form]);

    }

    // #[Route('/edit/{id}')]
    // /**
    //  * Edit a trick
    //  *
    //  * @param Figures $figures
    //  * @return Response
    //  */
    // public function edit(Figures $figures) :Response
    // {

    // }


    #[Route('/suppression/{id}', name:'delete')]
    /**
     * Delete a trick
     *
     * @param Figures $figures
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Figures $figures, EntityManagerInterface $entityManager) :Response
    {
        if (!$figures) {
            $this->addFlash('danger', "Cette figure n'existe pas");
            return $this->redirectToRoute('home');
        }

        $entityManager->remove($figures);
        $entityManager->flush();
        $this->addFlash('success', "Suppression réussit 😊");
        return $this->redirectToRoute('home');
    }

}