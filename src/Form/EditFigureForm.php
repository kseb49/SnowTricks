<?php

namespace App\Form;

use App\Entity\Groups;
use App\Entity\Figures;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditFigureForm extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add(
            'name',
            TextType::class,
            [
                "label" => "Nom de la figure",
                'required' => false,
                'constraints' => [
                    new Length(
                        [
                            'min' => 3,
                            'max'  => 100
                        ]
                        ),
                    new NotNull(['message' => 'Votre figure doit avoir un nom']),
                    new NotBlank(),
                ]
            ]
        )
        ->add(
            'description',
            TextareaType::class,
            [
                "label" => "Description",
                'required' => false,
                'constraints' => [
                    new Length(
                        [
                            'min' => 50,
                            'max' => 25000,
                        ],
                    ),
                    new NotBlank(),
                    new NotNull(['message' => 'Votre figure doit avoir une description']),
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
                'required' => false,
                "constraints" => [
                    new NotNull(['message' => 'Votre figure doit avoir une catégorie']),
                    new NotBlank(),
                ],
            ]
        );

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Figures::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]
        );

    }


}
