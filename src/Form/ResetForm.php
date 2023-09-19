<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class ResetForm extends AbstractType 
{


    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add('email', EmailType::class,[
            'mapped' => true,
            'help' => 'Saisissez votre email',
            'required' => true,
            'label' => "Email de votre compte",
        ]);

    }


    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Users::class,
    //     ]);

    // }


}