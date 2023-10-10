<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Figures;
use App\Form\ImageForm;
use App\Form\AddImageForm;
use App\Service\ImageManager;
use App\Service\Parameters;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class ImageController extends AbstractController
{


    public function __construct(public Parameters $parameters)
    {

    }


    #[Route('/ajout-image/{trick_id}', name:'add_image')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    /**
     * Add an image to a trick
     *
     * @param EntityManagerInterface $entityManager
     * @param Request                $request
     * @param integer                $trick_id
     * @param ImageManager           $manager
     * @param ImagesRepository       $imrepo
     * @return Response
     */
    public function addImage(EntityManagerInterface $entityManager, Request $request, int $trick_id, ImageManager $manager) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($trick_id);
        if (count($figure->getImages()) >= $this->getParameter('IMAGES_MAX')) {
            $this->addFlash('warning', $this->parameters->getMessages('errors', ['max_reach' => 'image']));
            return $this->redirectToRoute('figuresdetails', ["slug" => $figure->getSlug()]);
        }

        $form = $this->createForm(AddImageForm::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true  && $form->isValid() === true) {
            $images = $form->get('images')->getData();
            if ($images) {
                foreach ($images as $value) {
                    // The maximum number of images allowed.
                    if (count($figure->getImages()) >= $this->getParameter('IMAGES_MAX')) {
                        $this->addFlash('warning',$this->parameters->getMessages('errors', ['max_reach' => 'image']));
                        return $this->redirectToRoute('figuresdetails',["slug" => $figure->getSlug()]);
                    }
                    // Rename and Download the file.
                    try {
                        $images_name = $manager->upload($value,'figures_directory');
                    } catch (FileException $e) {
                        $this->addFlash('danger',$e);
                        return $this->redirectToRoute('home');
                    }

                    $picture = new Images;
                    $picture->setImageName($images_name);
                    $figure->addImage($picture);
                    $entityManager->persist($figure);
                    $entityManager->flush();
                }

                $this->addFlash('success', $this->parameters->getMessages('feedback', ['success' => 'image']));
                return $this->redirectToRoute('figuresdetails',["slug" => $figure->getSlug()]);
            }

            $this->addFlash('warning', $this->parameters->getMessages('feedback', ['edit' => 'missing']));
            return $this->render('edition/add_image_form.html.twig', ['add_image_form' => $form, 'figure' => $figure]);
        }

        return $this->render('edition/add_image_form.html.twig', ['add_image_form' => $form, 'figure' => $figure]);

    }


    #[Route('/suppression-image/{id}/{image_id}', name:'delete_image')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    /**
     * Delete an image
     *
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param integer                $id
     * @param integer                $image_id
     * @param ImageManager           $manager
     * @return Response
     */
    public function deleteImage(Request $request, EntityManagerInterface $entityManager, int $id, int $image_id, ImageManager $manager) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $image = $entityManager->getRepository(Images::class)->find($image_id);
        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
            // If there are more than one image.
            if (count($figure->getImages()) > 1) {
                $figure->removeImage($image);
                $entityManager->remove($image);
                $entityManager->persist($figure);
                $entityManager->flush();
                // Delete the file unless it is the default one.
                if ($image->getImageName() !== $this->getParameter('FIGURE_IMG')) {
                    try {
                        $manager->delete('figures_directory', $image->getImageName());
                    } catch (\Exception $e) {
                        $this->addFlash('danger',$e);
                        return $this->redirectToRoute('home');
                    }
                }

                $this->addFlash('success', $this->parameters->getMessages('feedback', ['delete' => 'message']));
                return $this->redirectToRoute('figuresdetails', ['slug' => $figure->getSlug()]);
            }

            $this->addFlash('danger', $this->parameters->getMessages('feedback', ['only' => 'image']));
            return $this->redirectToRoute(
                'figuresdetails',
                ['slug' => $figure->getSlug()]);
        }

        $this->addFlash('warning', $this->parameters->getMessages('errors', ['authenticate' => 'access']));
        return $this->redirectToRoute('figuresdetails', ['slug' => $figure->getSlug()]);

    }


    #[Route('/modification-image/{id}/{image_id}',name:'edit_image')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    /**
     * Edit an image
     *
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param integer                $id ID of the trick
     * @param integer                $image_id Id of the image
     * @param ImageManager           $upload
     * @return Response
     */
    public function editImage(Request $request, EntityManagerInterface $entityManager, int $id, int $image_id, ImageManager $upload) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $form = $this->createForm(ImageForm::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $image = $form->get('images')->getData();
            if ($image) {
                try {
                    $images_name = $upload->upload($image,'figures_directory');
                } catch (FileException $e) {
                    $this->addFlash('danger',$e);
                    return $this->redirectToRoute('figuresdetails', ["slug" => $figure->getSlug()]);
                }

                $picture = new Images;
                $picture->setImageName($images_name);
                $figure->addImage($picture);
                $eximage = $entityManager->getRepository(Images::class)->find($image_id);
                $figure->removeImage($eximage);
                $entityManager->remove($eximage);
                // Delete the file too, unless the file is the default one.
                if ($eximage->getImageName() !== $this->getParameter('FIGURE_IMG')) {
                    try {
                        $upload->delete('figures_directory', $eximage->getImageName());
                    } catch (\Exception $e) {
                        $this->addFlash('danger',$e);
                        return $this->redirectToRoute('figuresdetails', ["slug" => $figure->getSlug()]);
                    }

                }

                $entityManager->persist($figure);
                $entityManager->flush();
                $this->addFlash('success',$this->parameters->getMessages('feedback',['edit' => 'message']));
                return $this->redirectToRoute('figuresdetails',['slug' => $figure->getSlug()]);
            }

            $this->addFlash('warning', $this->parameters->getMessages('feedback', ['edit' => 'missing']));
            return $this->render('edition/add_image_form.html.twig', ['add_image_form' => $form, 'figure' => $figure]);
        }

        return $this->render('edition/image_form.html.twig', ['image_form' => $form, 'figure' => $figure, 'img_id' => $image_id]);
    }


}
