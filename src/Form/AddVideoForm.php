<?php

namespace App\Form;

use App\Entity\Figures;
use App\Entity\Videos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class AddVideoForm extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add(
            'videos',
            CollectionType::class,
            [
                'mapped'=>false,
                'entry_type'=>VideoForm::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'entry_options' => ['label' => false],
            ]
        );

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Figures::class]);

    }


}
