<?php

namespace App\Controller;

use DateTime;
use App\Entity\Images;
use App\Entity\Videos;
use App\Entity\Figures;
use App\Entity\Messages;
use App\Form\FigureForm;
use App\Service\Parameters;
use App\Form\EditFigureForm;
use App\Form\AddMessagesForm;
use App\Service\ImageManager;
use App\Repository\ImagesRepository;
use App\Repository\VideosRepository;
use App\Repository\MessagesRepository;
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

    public function __construct(public Parameters $parameters){}


    #[Route('/{slug}', name:'details')]
    /**
    * Page of a single trick
    *
    * @param Request $request Http Request
    * @param Figures $figures Figure entity
    * @param EntityManagerInterface $entityManager Entitymanager Interface
    * @param MessagesRepository $messageRepository MessageRepository
    * @return Response
    */
    public function details(Request $request, Figures $figures, EntityManagerInterface $entityManager, MessagesRepository $messagesRepository) :Response
    {
        if (!$figures) {
            $this->addFlash('danger', $this->parameters->getMessages('errors', ['unknown' => 'message']));
            return $this->redirectToRoute('home');
        }
        // Comment form.
        $message = new Messages();
        $form = $this->createForm(AddMessagesForm::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $this->denyAccessUnlessGranted('ROLE_USER');
            $message->setContent($form->get('content')->getData());
            $message->setMessageDate();
            $message->setUsers($this->getUser());
            $figures->addMessage($message);
            $entityManager->persist($figures);
            $entityManager->flush();
            $this->addFlash('success', $this->parameters->getMessages('feedback', ['success' => 'comment']));
            return $this->redirectToRoute('figuresdetails', ['slug' => $figures->getSlug()]);
        }

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $messagesRepository->findPaginated($figures, $offset);
        return $this->render('details.html.twig', ['figures' => $figures, 'default_image' => $this->parameters::DEFAULT_IMAGE, 'message_form' => $form, 'messages' => $paginator, 'previous' => $offset - MessagesRepository::PAGINATOR_PER_PAGE, 'next' => min(count($paginator), $offset + MessagesRepository::PAGINATOR_PER_PAGE)]);

    }


    #[Route('/creation-figure', name:'create', priority: 1)]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    /**
     * Create a trick
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ImageManager $upload Imagemanager service
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $entityManager, ImageManager $upload, SluggerInterface $slugger) :Response
    {
        $figure = new Figures();
        $form = $this->createForm(FigureForm::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
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
            } else {
                // set the default image.
                $picture = new Images;
                $picture->setImageName($this->parameters::DEFAULT_IMAGE);
                $figure->addImage($picture);
            }
            $videos = $form->get('videos')->getData();
            if ($videos) {
                foreach ($videos as $value) {
                    $embed = new Videos;
                    $embed->setSrc($value->getSrc());
                    $figure->addVideos($embed);
                }
            }
            $figure->setName($form->get('name')->getData());
            $figure->setSlug(strtolower($slugger->slug($form->get('name')->getData())));
            $figure->setDescription($form->get('description')->getData());
            $figure->setCreationDate();
            $figure->setUsersId($this->getUser());
            $figure->setGroupsId($form->get('groups_id')->getData());
            $entityManager->persist($figure);
            $entityManager->flush();
            $this->addFlash('success', $this->parameters->getMessages('feedback', ['success' => 'figure']));
            return $this->redirectToRoute('home');
        }

    return $this->render(
        'edition/new_figure.html.twig',
        ['figure_form' => $form]);

    }


    #[Route('/modification/{id}',name:'edit')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    /**
     * Edit a trick - All the text parts and the related group
     *
     * @param Figures $figures
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, int|string $id, Figures $figure, SluggerInterface $slugger) :Response
    {
        $figure = $entityManager->getRepository(Figures::class)->find($id);
        $form = $this->createForm(EditFigureForm::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $figure->setUpdateDate(new DateTime());
            $figure->setSlug(strtolower($slugger->slug($form->get('name')->getData())));
            $entityManager->persist($figure);
            $entityManager->flush();
            $this->addFlash('success',$this->parameters->getMessages('feedback', ['edit' => 'message']));
            return $this->redirectToRoute('figuresdetails', ['slug' => $figure->getSlug()]);
        }
        return $this->render(
            'edition/edit_figure.html.twig',
            ['figure_form' => $form]);

    }


    #[Route('/suppression/{id}', name:'delete')]
    #[IsGranted('ROLE_USER', message:"Veuillez confirmer votre compte")]
    /**
     * Delete a trick
     *
     * @param Figures $figures
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Figures $figures, EntityManagerInterface $entityManager, ImageManager $manager, Request $request) :Response
    {
        if (!$figures) {
            $this->addFlash('danger', $this->parameters->getMessages('errors', ['unknown' => 'message']));
            return $this->redirectToRoute('home');
        }
        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
            //Delete the file
            foreach ($figures->getImages() as $value) {
                if ($value->getImageName() !== $this->parameters::DEFAULT_IMAGE) {
                    $manager->delete('figures_directory',$value->getImageName());
                }
            }

            $entityManager->remove($figures);
            $entityManager->flush();
            $this->addFlash('success', $this->parameters->getMessages('feedback', ['delete' => 'message']));
            return $this->redirectToRoute('home');
        }

        $this->addFlash('warning', $this->parameters->getMessages('errors', ['authenticate' => 'access']));
        return $this->redirectToRoute('figuresdetails', ['slug' => $figures->getSlug()]);

    }


}