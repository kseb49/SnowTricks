<?php

namespace App\Controller;

use DateTime;
use App\Entity\Images;
use App\Entity\Videos;
use App\Entity\Figures;
use App\Form\ImageForm;
use App\Form\FigureForm;
use App\Form\EditFigureForm;
use App\Repository\FiguresRepository;
use App\Repository\ImagesRepository;
use App\Service\ImageManager;
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
    
    /**
     * The default image used when the request hasn't any
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
            'figures' => $figures, 'default_image' => self::DEFAULT_IMG]);
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
                $picture->setImageName(self::DEFAULT_IMG);
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
     * Edit a trick - All the text part and the related group
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

  
    #[Route('/modification-image/{id}/{image_id}',name:'edit_image')]
    /**
     * Edit an image
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param integer $id ID of the trick
     * @param integer $image_id Id of the image
     * @param ImageManager $upload
     * @return Response
     */
    public function editImage(Request $request, EntityManagerInterface $entityManager,int $id, int $image_id, ImageManager $upload) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $form = $this->createForm(ImageForm::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('images')->getData();
            if ($image) {
                try {
                    $images_name = $upload->upload($image,'figures_directory');
                    $picture = new Images;
                    $picture->setImageName($images_name);
                    $figure->addImage($picture);
                    $eximage = $entityManager->getRepository(Images::class)->find($image_id);
                    $figure->removeImage($eximage);
                    // Delete the file too, unless the file is the default one
                    if ($eximage->getImageName() !== self::DEFAULT_IMG) {
                        $upload->delete('figures_directory',$eximage->getImageName());
                    }
                    $entityManager->persist($figure);
                    $entityManager->flush();
                } catch (FileException $e) {
                    return $this->redirectToRoute('figuresdetails', ["error" => $e, "figures" => $figure, 'default_image' => self::DEFAULT_IMG]);
                }
                $this->addFlash('success', "L'image a été correctemnt modifiée 😊");
                return $this->redirectToRoute('figuresdetails',['slug' =>$figure->getSlug()]);
            }
        }
        return $this->render('edition/image_form.html.twig', [
            'image_form' => $form, 'figure' =>  $figure, 'img_id' => $image_id]);

    }

    #[Route('/modification-video/{id}/{video_id}',name:'edit_video')]
    public function editVideo(Request $request, EntityManagerInterface $entityManager,int $id, int $image_id) :Response
    {

        return $this->render('edition/image_form.html.twig', [
            'image_form' => $form, 'figure' =>  $figure]);

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


    #[Route('/suppression-image/{id}/{image_id}', name:'image_delete')]
    #[IsGranted('ROLE_USER', message:"Connectez vous pour supprimer une image")]
    public function deleteImage(EntityManagerInterface $entityManager,int $id, int $image_id,ImageManager $manager,ImagesRepository $imrepo) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $image = $entityManager->getRepository(Images::class)->find($image_id);
        // if there are more than one image
        if ($imrepo->findImages($id)[1] > 1) {
            $figure->removeImage($image);
            $entityManager->persist($figure);
            $entityManager->flush();
            if ($image->getImageName() !== self::DEFAULT_IMG) {
                $manager->delete('figures_directory',$image->getImageName());
            }
            $this->addFlash('success', "Suppression réussit 😊");
            return $this->redirectToRoute('figuresdetails', [
                'slug' => $figure->getSlug()]);
        }
        $this->addFlash('danger', "Cette image ne peut pas être supprimé car c'est la seule pour ce trick");
            return $this->redirectToRoute('figuresdetails', [
                'slug' => $figure->getSlug()]);
        }
        
        #[Route('/suppression-video/{id}/{video_id}',name:'video_delete')]
        public function deleteVideo(){}
    }