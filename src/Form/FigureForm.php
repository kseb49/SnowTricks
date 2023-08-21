<?php

namespace App\Form;

use App\Entity\Videos;
use App\Entity\Figures;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FigureForm extends AbstractType 
{


    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add('name',TextType::class,["label" => "Nom de la figure"])
        ->add('description',TextareaType::class,["label" => "Description"])
        ->add('videos',TextType::class,['mapped' => false])
        ->add('images',FileType::class,[
            'mapped' => false,
            'required' => false,
            'multiple' => true,
            'constraints' => [
                new Image([
                    'maxSize' => '5000k',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/gif',
                        'image/png',
                        'image/webp'
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image {{types}}',
                ])
                ]
        ]
                )
                ->add('creer',SubmitType::class);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figures::class,
        ]);
    }
}