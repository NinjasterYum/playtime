<?php

namespace App\Form;

use App\Entity\GuestReservation;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Terrain;

class ManualReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $company = $options['company'];

        $builder
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'query_builder' => function ($er) use ($company) {
                    return $er->createQueryBuilder('s')
                        ->where('s.sportCompany = :company')
                        ->setParameter('company', $company);
                },
                'label' => 'Service',
            ])

            ->add('terrain' , EntityType::class, [
                'class' => Terrain::class,
                'choice_label' => 'name',
                'query_builder' => function ($er) use ($company) {
                    return $er->createQueryBuilder('t')
                        ->where('t.sportCompany = :company')
                        ->setParameter('company', $company);
                },
                'label' => 'Terrain',
            ])

            ->add('dateTime', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'datetimepicker'],
                'label' => 'Date et heure',
            ])
            ->add('clientFirstName', TextType::class, [
                'label' => 'Prénom du client',
            ])
            ->add('clientLastName', TextType::class, [
                'label' => 'Nom du client',
            ])
            ->add('clientEmail', EmailType::class, [
                'label' => 'Email du client',
            ])
            ->add('clientPhone', TelType::class, [
                'label' => 'Téléphone du client',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GuestReservation::class,
            'company' => null,
        ]);

        $resolver->setRequired('company');
    }
}