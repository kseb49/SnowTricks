<?php

namespace App\Controller;

use DateTime;
use App\Entity\Users;
use App\Service\SendEmail;
use App\Service\Parameters;
use App\Service\ImageManager;
use App\Form\RegistrationFormType;
use App\Form\ResetForm;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;


class RegistrationController extends AbstractController //https://symfony.com/doc/current/forms.html#processing-forms
{


    #[Route('/inscription', name: 'app_register')]
    public function register(Parameters $parameters, Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, EntityManagerInterface $entityManager, ImageManager $fileUploader, SendEmail $mail): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                try {
                    $photo_name = $fileUploader->upload($photo,'avatars_directory');

                } catch (FileException $e) {
                    return $this->redirectToRoute('app_register',["error" => $e]);

                }
                $user->setPhoto($photo_name);

            }
            else {
                $user->setPhoto();
            }
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setEmail($form->get('email')->getData());
            $user->setName($form->get('name')->getData());
            $token = hash('md5',uniqid(true));
            $user->setToken($token);
            $user->setSendLink(new DateTime());
            $entityManager->persist($user);
            $entityManager->flush();
            try {
                $mail->sendEmail(to: $user->getEmail(), subject : $parameters->getMailParameters($parameters::CONFIRM)['sujet'], template: $parameters->getMailParameters($parameters::CONFIRM)['template'], context:['mail' => $user->getEmail(), 'token' => $token, 'route' => $parameters->getMailParameters($parameters::CONFIRM)['route']]);

            } catch (TransportExceptionInterface $e) {
                $this->addFlash('warning', $e);
                return $this->redirectToRoute('home');
            }
            $this->addFlash('success', 'Vous avez 24 heures pour confirmez votre email');
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }

    #[Route('/confirmation', name: 'account_confirmation')]
    public function confirm(Request $request, EntityManagerInterface $entityManager, Parameters $parameters, SendEmail $mail) :Response
    {
        if ($user = $entityManager->getRepository(Users::class)->findOneBy(['email' => $request->query->get('mail')])) {
            if ($user->getConfirmationDate() === null) {
                $limit = $user->getsendLink();
                $now = new DateTime(date('Y-m-d H:i:s'));
                $diff = $limit->diff($now);
                // The link must be less than 24hrs.
                if ($diff->format("%D") < 1 && $request->query->get('token') === $user->getToken()) {
                    $user->setConfirmationDate(new DateTime());
                    $user->setSendLink(null);
                    $user->setToken(null);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->addFlash('success', 'Votre compte est confirmé');
                    return $this->redirectToRoute('home');
                }
                try {
                    $token = hash('md5',uniqid(true));
                    $user->setToken($token);
                    $user->setSendLink(new DateTime());
                    $mail->sendEmail(to: $user->getEmail(), subject : $parameters->getMailParameters($parameters::CONFIRM)['sujet'], template: $parameters->getMailParameters($parameters::CONFIRM)['template'], context:['mail' => $user->getEmail(), 'token' => $token, 'route' => $parameters->getMailParameters($parameters::CONFIRM)['route']]);
                    $entityManager->persist($user);
                    $entityManager->flush();
     
                } catch(TransportExceptionInterface $e) {
                    $this->addFlash('warning', $e);
                    return $this->redirectToRoute('home');
                }
                $this->addFlash('danger', 'Ce lien n\'est pas valable. Un nouveau vous a été envoyé à votre adresse mail');
                return $this->redirectToRoute('home');
            }
            $this->addFlash('warning', 'Votre compte est dèjà confirmé');
            return $this->redirectToRoute('home');
        }
        // The user mail doesn't exist in the DB.
        $this->addFlash('warning', "Ce lien n'est pas valable");
        return $this->redirectToRoute('home');

    }

    #[Route('reset-password', 'reset')]
    public function reset(Request $request, EntityManagerInterface $entityManager, SendEmail $mail) :Response
    {
        if($this->getUser() !== null){
            $this->addFlash('danger', "Vous ne pouvez pas accéder à cette page si vous êtes connecté");
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(ResetForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           if ($find = $entityManager->getRepository(Users::class)->findOneBy(['email' => $form->get('email')->getData()])) {
                
           }
        }

        return $this->render('security/reset.html.twig', ['form' => $form]);
}
}