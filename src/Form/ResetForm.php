<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;




class ResetForm extends AbstractType 
{


    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add('name', TextType::class,[
            'mapped' => true,
            'help' => "Saisissez votre nom d'utilisateur",
            'required' => true,
            'label' => "Pseudo de votre compte",
        ]);

    }


    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Users::class,
    //     ]);

    // }


}