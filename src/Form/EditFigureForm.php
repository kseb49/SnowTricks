<?php

namespace App\Form;

use App\Entity\Groups;
use App\Entity\Figures;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditFigureForm extends AbstractType 
{


    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add('name',TextType::class,["label" => "Nom de la figure", 'constraints' => [new Length(['min' => 3, 'max'  => 100])]])
        ->add('description',TextareaType::class,["label" => "Description", 'constraints' => [new Length(['min' => 50])]])
        ->add('groups_id',EntityType::class,[
            'class' => Groups::class,
            'choice_label' => 'group_name',
            'label' => 'A quel groupe appartient ce trick?',
            'required' => true]);

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figures::class,
        ]);

    }


}