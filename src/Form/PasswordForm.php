<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordForm extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add(
            'password',
            RepeatedType::class,
            [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répétez votre mot de passe'],
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 6])
                ]
            ]
        );

    }


}
