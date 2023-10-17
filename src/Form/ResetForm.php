<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;




class ResetForm extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add(
            'name',
            TextType::class,
            [
                'mapped' => true,
                'help' => "Saisissez votre nom d'utilisateur",
                'required' => false,
                'label' => "Pseudo de votre compte",
                "constraints" => [
                    new NotBlank(),
                ],
            ]
        );

    }


}