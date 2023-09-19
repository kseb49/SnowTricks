<?php

namespace App\Controller;

use App\Entity\Figures;
use App\Entity\Messages;
use App\Service\Parameters;
use App\Form\AddMessagesForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

class MessagesController extends AbstractController
{

    // public function __construct(private EntityManagerInterface $entityManager)
    // {
        
    // }
    // #[Route('ajout-message',name:'add-message')]
    // #[IsGranted('ROLE_USER', message:"Connectez vous pour commenter")]
    // public function getMessage(FormInterface $form, Figures $figures, Messages $message) :Response
    // {
    //     if($form->isValid()) {
    //         $message->setContent($form->get('content')->getData());
    //         $message->setMessageDate();
    //         $message->setUsers($this->getUser());
    //         $figures->addMessage($message);
    //         $this->entityManager->persist($figures);
    //         $this->entityManager->flush();
    //         $this->addFlash('success', "Votre commentaire est en ligne 😊");
    //         return $this->redirectToRoute('figuresdetails', ['slug' => $figures->getSlug()]);
    //     }

    //     return $this->redirectToRoute('figuresdetails', ['slug' => $figures->getSlug()]);

    // }


}
    // public function addMessage(Request $request, EntityManagerInterface $entityManager, int $id, Parameters $parameters)
    // {

    //     $figure = $entityManager->getRepository(Figures::class)->find($id);
    //     // $params = $this->json(['id' => $figure->getId()]);
    //     $message = new Messages();
    //     $form = $this->createForm(AddMessagesForm::class, $message);
    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $message->setContent($form->get('content')->getData());
    //         $message->setMessageDate();
    //         $message->setUsers($this->getUser());
    //         $figure->addMessage($message);
    //         $entityManager->persist($figure);
    //         $entityManager->flush();
    //         $this->addFlash('success', "Votre commentaire est en ligne 😊");
    //         return $this->redirectToRoute('figuresdetails', ['slug' => $figure->getSlug()]);
    //     }

    //         return $this->display('edition/add_comment.html.twig',['message_form' => $form, 'figure' => $id]);
    //         // return $this->render('details.html.twig', [
    //         //     'figures' => $figure, 'default_image' => $parameters::DEFAULT_IMG]);

    // }



