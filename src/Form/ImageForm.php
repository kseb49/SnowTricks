<?php

namespace App\Form;

use App\Entity\Figures;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class ImageForm extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add(
            'images',
            FileType::class,
            [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File(
                        [
                            'maxSize' => '10000k',
                            'mimeTypes' =>
                                [
                                    'image/jpeg',
                                    'image/gif',
                                    'image/png',
                                    'image/webp',
                                ],
                            'mimeTypesMessage' => "Ce type de fichier n'est pas autorisé",
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
