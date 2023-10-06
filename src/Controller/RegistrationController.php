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


class RegistrationController extends AbstractController
{
    public function __construct(public Parameters $parameters){}


    #[Route('/inscription', name: 'app_register')]
    /**
     * Register an user
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param UserAuthenticatorInterface $userAuthenticator
     * @param UsersAuthenticator $authenticator
     * @param EntityManagerInterface $entityManager
     * @param ImageManager $fileUploader
     * @param SendEmail $mail
     * @return Response
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, EntityManagerInterface $entityManager, ImageManager $fileUploader, SendEmail $mail): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                try {
                    $photo_name = $fileUploader->upload($photo,'avatars_directory');

                } catch (FileException $e) {
                    return $this->redirectToRoute('app_register', ["error" => $e]);

                }

                $user->setPhoto($photo_name);
            } else {
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
            // Send email with confirmation link.
            try {
                $mail->sendEmail(to: $user->getEmail(), subject : $this->parameters->getMailParameters($this->parameters::CONFIRM)['sujet'], template: $this->parameters->getMailParameters($this->parameters::CONFIRM)['template'], context:['mail' => $user->getEmail(), 'token' => $token, 'route' => $this->parameters->getMailParameters($this->parameters::CONFIRM)['route']]);
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


    /**
     * Confirm an account by managing the confirmation email sent
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SendEmail $mail
     * @return Response
     */
    #[Route('/confirmation', name: 'account-confirmation')]
    public function confirm(Request $request, EntityManagerInterface $entityManager, SendEmail $mail) :Response
    {
        if ($user = $entityManager->getRepository(Users::class)->findOneBy(['email' => $request->query->get('mail')])) {
            // If Account not confirmed.
            if ($user->getConfirmationDate() === null && $user->getsendLink() !== null) {
                $limit = $user->getsendLink();
                $now = new DateTime(date('Y-m-d H:i:s'));
                $diff = $limit->diff($now);
                // The link must be less than 24hrs and the tokens must match (*).
                if ($diff->format("%D") < 1 && $request->query->get('token') === $user->getToken()) {
                    $user->setConfirmationDate(new DateTime());
                    $user->setSendLink(null);
                    $user->setToken(null);
                    $user->setRoles(['ROLE_USER']);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->addFlash('success', $this->parameters->getMessages('feedback', ['user' => 'confirm']));
                    return $this->redirectToRoute('home');
                }
                // * Send a new link if not.
                $token = hash('md5',uniqid(true));
                $user->setToken($token);
                $user->setSendLink(new DateTime());
                try {
                    $mail->sendEmail(to: $user->getEmail(), subject : $this->parameters->getMailParameters($this->parameters::CONFIRM)['sujet'], template: $this->parameters->getMailParameters($this->parameters::CONFIRM)['template'], context:['mail' => $user->getEmail(), 'token' => $token, 'route' => $this->parameters->getMailParameters($this->parameters::CONFIRM)['route']]);
                } catch(TransportExceptionInterface $e) {
                    $this->addFlash('warning', $e);
                    return $this->redirectToRoute('home');
                }
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('danger', $this->parameters->getMessages('errors', ['link' => 'expired']));
                return $this->redirectToRoute('home');
            }

            $this->addFlash('warning', $this->parameters->getMessages('feedback', ['user' => 'ever']));
            return $this->redirectToRoute('home');
        }

        // The user mail doesn't exist in the DB. Remain unclear
        $this->addFlash('warning', $this->parameters->getMessages('errors', ['link' => 'invalid']));
        return $this->redirectToRoute('home');

    }

    /**
     * Manage the user reset password request
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SendEmail $mail
     * @return Response
     */
    #[Route('reset-password', 'reset')]
    public function reset(Request $request, EntityManagerInterface $entityManager, SendEmail $mail) :Response
    {
        if ($this->getUser() !== null) {
            $this->addFlash('danger', $this->parameters->getMessages('errors', ['authenticate' => 'wrong']));
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ResetForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
           if ($user = $entityManager->getRepository(Users::class)->findOneBy(['name' => $form->get('name')->getData()])) {
                // If Account not confirmed.
               if ($user->getConfirmationDate() === null ) {
                    $this->addFlash('warning', $this->parameters->getMessages('feedback', ['user' => 'before']));
                    return $this->redirectToRoute('home');
               }

               $token = hash('md5',uniqid(true));
               $user->setToken($token);
               $user->setSendLink(new DateTime());
               $entityManager->persist($user);
               $entityManager->flush();
               try {
                    $mail->sendEmail(to: $user->getEmail(), subject : $this->parameters->getMailParameters($this->parameters::RESET)['sujet'], template: $this->parameters->getMailParameters($this->parameters::RESET)['template'], context:['mail' => $user->getEmail(), 'token' => $token, 'route' => $this->parameters->getMailParameters($this->parameters::RESET)['route']]);
                } catch (TransportExceptionInterface $e) {
                    $this->addFlash('warning', $e);
                    return $this->redirectToRoute('reset');
                }
                $this->addFlash('success', $this->parameters->getMailParameters($this->parameters::RESET)['message']);
                return $this->redirectToRoute('home');

            }

            $this->addFlash('danger', $this->parameters->getMessages('feedback', ['user' => 'unknown']));
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset.html.twig', ['form' => $form]);
    }


    /**
     * Reset the password
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param SendEmail $mail
     * @return void
     */
    #[Route('confirm-reset', 'password-reset')]
    public function confirmReset(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, SendEmail $mail)
    {
        if ($user = $entityManager->getRepository(Users::class)->findOneBy(['email' => $request->query->get('mail')])) {
            // The account has been confirmed by the user and a link is pending to be processed.
            if ($user->getSendLink() !== null && $user->getConfirmationDate() !== null) {
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
                            $this->addFlash('success', $this->parameters->getMessages('feedback', ['success' => 'password']));
                            return $this->redirectToRoute('home');
                        }
                        return $this->render('security/new_password.html.twig', ['form' => $form]);
                    }
                    $this->addFlash('danger', $this->parameters->getMessages('errors', ['link' => 'invalid']));
                    return $this->redirectToRoute('app_login');
                }
                // Expired link
                $token = hash('md5',uniqid(true));
                $user->setToken($token);
                $user->setSendLink(new DateTime());
                $entityManager->persist($user);
                $entityManager->flush();
                try {
                    $mail->sendEmail(to: $user->getEmail(), subject : $this->parameters->getMailParameters($this->parameters::RESET)['sujet'], template: $this->parameters->getMailParameters($this->parameters::RESET)['template'], context:['mail' => $user->getEmail(), 'token' => $token, 'route' => $this->parameters->getMailParameters($this->parameters::RESET)['route']]);
                } catch (TransportExceptionInterface $e) {
                    $this->addFlash('warning', $e);
                    return $this->redirectToRoute('reset');
                }

                $this->addFlash('danger', $this->parameters->getMessages('errors', ['link' => 'expired']));
                return $this->redirectToRoute('home');
            }

            $this->addFlash('danger', $this->parameters->getMessages('errors', ['link' => 'invalid']));
            return $this->redirectToRoute('home');
        }

        // The user is unknown. Remain unclear.
        $this->addFlash('danger', $this->parameters->getMessages('errors', ['link' => 'invalid']));
        return $this->redirectToRoute('app_login');

    }


}