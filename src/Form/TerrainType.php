<?php

namespace App\Form;

use App\Entity\Terrain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TerrainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du terrain'
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de terrain',
                'choices' => [
                    'Naturel' => 'naturel',
                    'Synthétique' => 'synthetique'
                ]
            ])
            ->add('isIndoor', ChoiceType::class, [
                'label' => 'Emplacement',
                'choices' => [
                    'Intérieur' => true,
                    'Extérieur' => false
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Terrain::class,
        ]);
    }
}