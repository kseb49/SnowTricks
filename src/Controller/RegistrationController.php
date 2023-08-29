<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthenticator;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController //https://symfony.com/doc/current/forms.html#processing-forms
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, EntityManagerInterface $entityManager, ImageUploader $fileUploader): Response
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
            $entityManager->persist($user);
            $entityManager->flush();
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
            // do anything else you need here, like send an email
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }
}
