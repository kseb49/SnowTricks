<?php

namespace App\Controller;

use DateTime;
use App\Entity\Users;
use App\Form\ResetForm;
use App\Form\PasswordForm;
use App\Service\SendEmail;
use App\Service\Parameters;
use App\Service\ImageManager;
use App\Form\RegistrationFormType;
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


    #[Route('/confirmation', name: 'account-confirmation')]
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
                    $this->addFlash('success', $parameters->getMessages('feedback', ['user' => 'confirm']));
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
                $this->addFlash('danger', $parameters->getMessages('errors', ['link' => 'expired']));
                return $this->redirectToRoute('home');
            }
            $this->addFlash('warning', $parameters->getMessages('feedback', ['user' => 'ever']));
            return $this->redirectToRoute('home');
        }
        // The user mail doesn't exist in the DB.
        $this->addFlash('warning', $parameters->getMessages('errors', ['link' => 'invalid']));
        return $this->redirectToRoute('home');

    }


    #[Route('reset-password', 'reset')]
    public function reset(Request $request, EntityManagerInterface $entityManager, SendEmail $mail,Parameters $parameters) :Response
    {
        if($this->getUser() !== null){
            $this->addFlash('danger', "Déconnectez vous pour accéder à cette page");
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(ResetForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           if ($user = $entityManager->getRepository(Users::class)->findOneBy(['name' => $form->get('name')->getData()])) {
               if ($user->getConfirmationDate() !== null && $user->getSendLink() === null) {
                   try {
                        $token = hash('md5',uniqid(true));
                        $user->setToken($token);
                        $user->setSendLink(new DateTime());
                        $entityManager->persist($user);
                        $entityManager->flush();
                        $mail->sendEmail(to: $user->getEmail(), subject : $parameters->getMailParameters($parameters::RESET)['sujet'], template: $parameters->getMailParameters($parameters::RESET)['template'], context:['mail' => $user->getEmail(), 'token' => $token, 'route' => $parameters->getMailParameters($parameters::RESET)['route']]);
                        $this->addFlash('success', $parameters->getMailParameters($parameters::RESET)['message']);
                        return $this->redirectToRoute('home');
                    } catch (TransportExceptionInterface $e) {
                        $this->addFlash('warning', $e);
                        return $this->redirectToRoute('reset');
                    }
                }
           }
            $this->addFlash('danger', "Erreur");
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset.html.twig', ['form' => $form]);
    }


    #[Route('confirm-reset', 'password-reset')]
    public function confirmReset(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, SendEmail $mail, Parameters $parameters)
    {
        if ($user = $entityManager->getRepository(Users::class)->findOneBy(['email' => $request->query->get('mail')])) {
            $limit = $user->getSendLink();
            $now = new DateTime(date('Y-m-d H:i:s'));
            $diff = $limit->diff($now);
            // The link must be less than 24hrs.
            if ($diff->format("%D") < 1) {
                if ($request->query->get('token') === $user->getToken()) {
                    $form = $this->createForm(PasswordForm::class);
                    $form->handleRequest($request);
                    if ($form->isSubmitted() && $form->isValid()) {
                        $user->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                $form->get('password')->getData()
                            )
                        );
                        $user->setToken(null);
                        $user->setSendLink(null);
                        $entityManager->persist($user);
                        $entityManager->flush();
                        $this->addFlash('success', $parameters->getMessages('feedback', ['success' => 'password']));
                        return $this->redirectToRoute('home');
                    }
                    return $this->render('security/new_password.html.twig', ['form' => $form]);
                }
                $this->addFlash('danger', $parameters->getMessages('errors', ['link' => 'invalid']));
                return $this->redirectToRoute('app_login');
            }
            $token = hash('md5',uniqid(true));
            $user->setToken($token);
            $user->setSendLink(new DateTime());
            $entityManager->persist($user);
            $entityManager->flush();
            $mail->sendEmail(to: $user->getEmail(), subject : $parameters->getMailParameters($parameters::RESET)['sujet'], template: $parameters->getMailParameters($parameters::RESET)['template'], context:['mail' => $user->getEmail(), 'token' => $token, 'route' => $parameters->getMailParameters($parameters::RESET)['route']]);
            $this->addFlash('success',$parameters->getMailParameters($parameters::RESET)['message']);
            return $this->redirectToRoute('home');
        }
        // The user is unknown
        $this->addFlash('danger', $parameters->getMessages('errors', ['link' => 'invalid']));
        return $this->redirectToRoute('app_login');

    }


}