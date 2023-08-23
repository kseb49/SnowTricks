<?php

namespace App\Form;

use App\Entity\Groups;
use App\Entity\Figures;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\All;

class FigureForm extends AbstractType 
{


    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add('name',TextType::class,["label" => "Nom de la figure"])
        ->add('description',TextareaType::class,["label" => "Description"])
        ->add('groups_id',EntityType::class,[
            'class' => Groups::class,
            'choice_label' => 'group_name',
            'label' => 'A quel groupe appartient ce trick?',
            'required' => true])
        ->add('videos', TextType::class,['mapped' => false,'label' => 'Liens vers des vidéos you tube',])
        ->add('images', FileType::class,[
            'mapped' => false,
            'help' => 'Une ou plusieurs images pour illustrer la figure',
            'required' => false,
            'multiple' => true,
            'label' => "Images d'illustrations",
            'constraints' => [
                new All([
                    new File([
                        'maxSize' => '5000k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/gif',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image {{types}}'
                    ])
                    ])
            ]

                    ]);
                
               
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figures::class,
            'csrf_protection' => true,
            'csrf_field_name' => 'token',
            'csrf_token_id'   => 'figure'
        ]);
    }
}