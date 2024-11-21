<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\SportCompany;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use App\Form\DataTransformer\TimeToStringTransformer;
use App\Entity\Terrain;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $company = $options['company'];

        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker'],
                'label' => 'Date',
            ])

            ->add('terrain', EntityType::class, [
                'class' => Terrain::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) use ($company) {
                    return $er->createQueryBuilder('t')
                        ->where('t.sportCompany = :company')
                        ->setParameter('company', $company);
                },
            ])

            ->add('time', ChoiceType::class, [
                'choices' => $this->getTimeChoices($company),
                'label' => 'Heure',
                'placeholder' => 'Choisissez une heure',
                'required' => true,
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) use ($company) {
                    return $er->createQueryBuilder('s')
                        ->where('s.sportCompany = :company')
                        ->setParameter('company', $company);
                },
            ]);
            $builder->get('time')->addModelTransformer(new TimeToStringTransformer());
        }

    private function getTimeChoices(SportCompany $company): array
    {
        $choices = [];
        foreach ($company->getSchedules() as $schedule) {
            $start = $schedule->getOpeningTime();
            $end = $schedule->getClosingTime();
            $interval = new \DateInterval('PT30M');
            $current = clone $start;

            while ($current <= $end) {
                $time = $current->format('H:i');
                $choices[$time] = $time;
                $current->add($interval);
            }
        }

        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'company' => null,
        ]);

        $resolver->setRequired('company');
    }
}