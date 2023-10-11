<?php

namespace App\Form;

use App\Entity\Groups;
use App\Entity\Figures;
use App\Form\VideoForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\NotNull;

class FigureForm extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add(
            'name',
            TextType::class,
            [
                "label" => "Nom de la figure",
                'constraints' => [
                    new Length(
                        ['min' => 3,
                        'max'  => 100]
                    )
                ]
            ]
        )
        ->add(
            'description',
            TextareaType::class,
            [
                "label" => "Description",
                'constraints' => [
                    new Length(
                        ['min' => 50,
                        'max' => 25000]
                    )
                ]
            ]
        )
        ->add(
            'groups_id',
            EntityType::class,
            [
                'class' => Groups::class,
                'choice_label' => 'group_name',
                'label' => 'A quel groupe appartient ce trick?',
                "constraints" => [
                    new NotNull(['message' => 'Votre figure doit avoir une catégorie']),
                    new NotBlank(),
                ],
            ],
        )
        ->add(
            'videos',
            CollectionType::class,
            [
                'mapped' => false,
                'entry_type' => VideoForm::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'entry_options' => ['label' => false],
            ]
        )
        ->add(
            'images',
            FileType::class,
            [
                'mapped' => false,
                'help' => 'Une ou plusieurs images pour illustrer la figure',
                'multiple' => true,
                'label' => "Images d'illustrations",
                'attr' => ['class' => 'fileInput'],
                'constraints' => [
                    new All(
                        [
                            new File(
                                [
                                    'maxSize' => '10000k',
                                    'mimeTypes' => [
                                        'image/jpeg',
                                        'image/gif',
                                        'image/png',
                                        'image/webp',
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
        $resolver->setDefaults(
            [
                'data_class' => Figures::class,
                'attr' => [
                    'novalidate' => 'novalidate', // comment me to reactivate the html5 validation!
                ],
            ]);

    }


}
