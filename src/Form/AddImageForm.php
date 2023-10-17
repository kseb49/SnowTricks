<?php

namespace App\Form;

use App\Entity\Figures;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class AddImageForm extends AbstractType 
{


    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add(
            'images',
            FileType::class,
            [
                'mapped' => false,
                'help' => 'Une ou plusieurs images pour illustrer la figure',
                'required' => false,
                'multiple' => true,
                'label' => "Images d'illustrations",
                'attr' => ['class' => 'fileInput'],
                'constraints' => [
                    new All(
                        [
                            new File(
                                [
                                    'maxSize' => '10000k',
                                    'mimeTypes' => 
                                    [
                                        'image/jpeg',
                                        'image/gif',
                                        'image/png',
                                        'image/webp'
                                    ],
                                    'mimeTypesMessage' => "Ce type de fichier n'est pas autorisé",
                                ]
                            )
                        ]
                    )
                ]
            ]
        );

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Figures::class]);

    }


}
