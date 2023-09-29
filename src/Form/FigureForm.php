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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

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
        ->add('videos', CollectionType::class,['mapped' => false, 'entry_type' => VideoForm::class, 'allow_add' => true,'by_reference' => false, 'allow_delete' => true, 'entry_options' => ['label' => false]])
        ->add('slug', HiddenType::class,['mapped' => false])
        ->add('images', FileType::class,[
            'mapped' => false,
            'help' => 'Une ou plusieurs images pour illustrer la figure',
            'required' => false,
            'multiple' => true,
            'label' => "Images d'illustrations",
            'constraints' => [
                new All([
                    new File([
                        'maxSize' => '10000k',
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