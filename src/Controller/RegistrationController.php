<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthenticator;
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
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {

        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $photo_name = preg_replace("#[0-9]#", "", pathinfo($photo->getClientOriginalName(),PATHINFO_FILENAME));
                $safeFilename = $slugger->slug($photo_name);
                $new_photo_name =  $safeFilename.'-'.uniqid().$photo->guessExtension();
                try {
                    $photo->move($this->getParameter('avatars_directory'),$new_photo_name);
                } catch (FileException $e) {
                    return $this->redirectToRoute('app_register',["error" => $e]);
                }
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setPhoto($new_photo_name);
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
