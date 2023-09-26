<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Figures;
use App\Form\ImageForm;
use App\Form\AddImageForm;
use App\Service\ImageManager;
use App\Service\Parameters;
use App\Repository\ImagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class ImageController extends AbstractController 
{


    #[Route('/ajout-image/{trick_id}', name:'add_image')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    /**
     * Add an image to a trick
     *
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param integer $trick_id
     * @param ImageManager $manager
     * @param ImagesRepository $imrepo
     * @param Parameters $parameters The variables needed in the app
     * @return Response
     */
    public function addImage(EntityManagerInterface $entityManager, Request $request, int $trick_id, ImageManager $manager, ImagesRepository $imrepo, Parameters $parameters) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($trick_id);
        $form = $this->createForm(AddImageForm::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true  && $form->isValid() === true) {
            $images = $form->get('images')->getData();
            if ($images) {
                foreach ($images as $value) {
                    try {
                        // The maximum number of images allowed.
                        if ($imrepo->countImages($trick_id)[1] >= $_ENV['IMAGES_MAX']) {
                            $this->addFlash('warning',$parameters->getMessages('errors',$parameters->max_images));
                            return $this->redirectToRoute('figuresdetails',["slug" => $figure->getSlug()]);
                        }

                        $images_name = $manager->upload($value,'figures_directory');
                        $picture = new Images;
                        $picture->setImageName($images_name);
                        $figure->addImage($picture);
                    } catch (FileException $e) {
                        return $this->redirectToRoute('home',["error" => $e]);
                    }
                    $entityManager->persist($figure);
                    $entityManager->flush();
                }
                $this->addFlash('success',$parameters->getMessages('feedback',['success' => 'images']));
                return $this->redirectToRoute('figuresdetails',["slug" => $figure->getSlug()]);
            }
        }
        return $this->render('edition/add_image_form.html.twig', ['add_image_form' => $form, 'figure' => $figure]);

    }


    #[Route('/suppression-image/{id}/{image_id}', name:'delete_image')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    /**
     * Delete the selected image
     *
     * @param EntityManagerInterface $entityManager
     * @param integer $id The trick id
     * @param integer $image_id
     * @param ImageManager $manager
     * @param ImagesRepository $imrepo
     * @return Response
     */
    public function deleteImage(EntityManagerInterface $entityManager, int $id, int $image_id, ImageManager $manager, ImagesRepository $imrepo, Parameters $parameters) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $image = $entityManager->getRepository(Images::class)->find($image_id);
        // If there are more than one image.
        if ($imrepo->countImages($id)[1] > 1) {
            $figure->removeImage($image);
            $entityManager->remove($image);
            $entityManager->persist($figure);
            $entityManager->flush();
            if ($image->getImageName() !== $_ENV['FIGURE_IMG']) {
                $manager->delete('figures_directory',$image->getImageName());
            }
            $this->addFlash('success', $parameters->getMessages('feedback',['delete' => 'message']));
            return $this->redirectToRoute('figuresdetails', ['slug' => $figure->getSlug()]);
        }

        $this->addFlash('danger', $parameters->getMessages('feedback',['only' => 'image']));
            return $this->redirectToRoute('figuresdetails', [
                'slug' => $figure->getSlug()]);

    }


    #[Route('/modification-image/{id}/{image_id}',name:'edit_image')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
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
    public function editImage(Request $request, EntityManagerInterface $entityManager, int $id, int $image_id, ImageManager $upload, Parameters $parameters) :Response
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
                    $entityManager->remove($eximage);
                    // Delete the file too, unless the file is the default one
                    if ($eximage->getImageName() !== $_ENV['FIGURE_IMG']) {
                        $upload->delete('figures_directory',$eximage->getImageName());
                    }
                    $entityManager->persist($figure);
                    $entityManager->flush();
                } catch (FileException $e) {
                    return $this->redirectToRoute('figuresdetails', ["error" => $e, "slug" => $figure->getSlug()]);
                }
                $this->addFlash('success',$parameters->getMessages('feedback',['edit' => 'message']));
                return $this->redirectToRoute('figuresdetails',['slug' =>$figure->getSlug()]);
            }
        }
        return $this->render('edition/image_form.html.twig', ['image_form' => $form, 'figure' =>  $figure, 'img_id' => $image_id]);
    }


}