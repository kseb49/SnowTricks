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


class MessagesController extends AbstractController
{

    // #[Route('/ajout-message/{id}', name: 'add_message')]
    // #[IsGranted('ROLE_USER', message:"Connectez vous pour commenter")]
    // public function addMessage(Request $request, EntityManagerInterface $entityManager, int $id, Parameters $parameters): Response
    // {

    //     // $figure = $entityManager->getRepository(Figures::class)->find($id);
    //     // $message = new Messages();
    //     // $form = $this->createForm(AddMessagesForm::class, $message);
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

    //         return $this->render('edition/add_comment.html.twig',['message_form' => $form, 'figure' => $id]);
    //         // return $this->render('details.html.twig', [
    //         //     'figures' => $figure, 'default_image' => $parameters::DEFAULT_IMG]);

    // }


}
