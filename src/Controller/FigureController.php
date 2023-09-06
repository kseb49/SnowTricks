<?php

namespace App\Controller;

use DateTime;
use App\Entity\Images;
use App\Entity\Videos;
use App\Entity\Figures;
use App\Form\FigureForm;
use App\Form\EditFigureForm;
use App\Repository\ImagesRepository;
use App\Repository\VideosRepository;
use App\Service\ImageManager;
use App\Controller\Parameters;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/figures',name:'figures')]
class FigureController extends AbstractController 
{

    public function __construct(private $parameters = new Parameters){}
   
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
            'figures' => $figures, 'default_image' => $this->parameters::DEFAULT_IMG]);
    }


    #[Route('/creation-figure', name:'create', priority: 1)]
    #[IsGranted('ROLE_USER', message:"Connectez vous pour créer une figure")]
    /**
     * Create a trick
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ImageManager $upload
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $entityManager,ImageManager $upload, SluggerInterface $slugger) :Response
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
                //set the default image
                $picture = new Images;
                $picture->setImageName($this->parameters::DEFAULT_IMG);
                $figure->addImage($picture);
            }
                $figure->setName($form->get('name')->getData());
                $figure->setSlug(strtolower($slugger->slug($form->get('name')->getData())));
                $figure->setDescription($form->get('description')->getData());
                $figure->setCreationDate();
                $figure->setUsersId($this->getUser());
                $figure->setGroupsId($form->get('groups_id')->getData());
                $videos = $form->get('videos')->getData();
                if($videos){
                    $videos = new Videos;
                    $videos->setSrc($form->get('videos')->getData());
                    $figure->addVideos($videos);
                }
                $entityManager->persist($figure);
                $entityManager->flush();
                $this->addFlash('success', "La figure est en ligne 😊");
                return $this->redirectToRoute('home');

            }

        return $this->render('edition/new_figure.html.twig', [
            'figure_form' => $form]);

    }


    #[Route('/modification/{id}',name:'edit')]
    #[IsGranted('ROLE_USER', message:"Connectez vous pour modifier une figure")]
    /**
     * Edit a trick - All the text parts and the related group
     *
     * @param Figures $figures
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, int|string $id,Figures $figure, SluggerInterface $slugger ) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $form = $this->createForm(EditFigureForm::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $figure->setUpdateDate(new DateTime());
            $figure->setSlug(strtolower($slugger->slug($form->get('name')->getData())));
            $entityManager->persist($figure);
            $entityManager->flush();
            $this->addFlash('success','modifé avec succès');
            return $this->redirectToRoute('home');
        }
        // $this->addFlash('danger','erreur');
        return $this->render('edition/edit_figure.html.twig', [
            'figure_form' => $form]);
    }


    #[Route('/suppression/{id}', name:'delete')]
    #[IsGranted('ROLE_USER', message:"Connectez vous pour supprimer une figure")]
    /**
     * Delete a trick
     *
     * @param Figures $figures
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Figures $figures, EntityManagerInterface $entityManager,ImagesRepository $imrepo, VideosRepository $virepo, ImageManager $manager,int $id) :Response
    {
        if (!$figures) {
            $this->addFlash('danger', "Cette figure n'existe pas");
            return $this->redirectToRoute('home');
        }
        // Delete the images files linked
        foreach($imrepo->findAllImages($id) as $value) {
            if($value['image_name'] !== $this->parameters::DEFAULT_IMG) {
                $manager->delete('figures_directory',$value['image_name']);
                $imrepo->removeImages($value['image_name']);
            }
        };
        // Delete the videos
        foreach ($virepo->findAllVideos($id) as $value) {
            $virepo->removeVideos($value['src']);
        }
        $entityManager->remove($figures);
        $entityManager->flush();
        $this->addFlash('success', "Suppression réussit 😊");
        return $this->redirectToRoute('home');
    }


        #[Route('/suppression-video/{id}/{video_id}',name:'video_delete')]
        public function deleteVideo(){}
    }