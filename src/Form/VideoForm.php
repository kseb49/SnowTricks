<?php

namespace App\Form;

use App\Entity\Videos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class VideoForm extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add(
            'src',
            UrlType::class,
            [
                'label' => 'Liens vers des vidéos you tube',
                'required' => false,
                "constraints" => [
                    new NotNull(
                        ['message' => 'Un lien vers une vidéo doit être renseigné ici'],
                    ),
                    new NotBlank(),
                ],
            ],
        );

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([ 'data_class' => Videos::class]);

    }


}
