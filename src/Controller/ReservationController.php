<?php

namespace App\Controller;
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

use App\Entity\Reservation;
use App\Entity\SportCompany;
use App\Form\ReservationType;
use App\Service\ReservationValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormError;
use App\Entity\StandardUser;

class ReservationController extends AbstractController
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    #[Route('/reservation/new/{id}', name: 'make_reservation')]
public function new(Request $request, EntityManagerInterface $entityManager, ReservationValidator $validator, SportCompany $company): Response
{
    $user = $this->getUser();
    if (!$user || !in_array('ROLE_USER', $user->getRoles())) {
        $this->addFlash('error', 'Vous devez être connecté en tant qu\'utilisateur standard pour faire une réservation.');
        return $this->redirectToRoute('home');
    }

    $standardUser = $entityManager->getRepository(StandardUser::class)->find($user->getId());
    if (!$standardUser) {
        $this->addFlash('error', 'Votre compte n\'est pas configuré correctement pour faire une réservation.');
        return $this->redirectToRoute('home');
    }

    $reservation = new Reservation();
    $reservation->setSportCompany($company);
    $reservation->setStandardUser($standardUser);
    $openingHours = $this->getOpeningHours($company);
    
    $form = $this->createForm(ReservationType::class, $reservation, [
        'company' => $company,
    ]);
    
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $validationResult = $validator->validate($reservation);
        if ($validationResult->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Réservation créée avec succès !');
            return $this->redirectToRoute('reservation_details', ['id' => $reservation->getId()]);
        } else {
            foreach ($validationResult->getErrors() as $error) {
                $form->addError(new FormError($error));
            }
        }
    }

    return $this->render('reservation/new.html.twig', [
        'form' => $form->createView(),
        'company' => $company,
        'openingHours' => json_encode($openingHours),
    ]);
}

    private function getOpeningHours(SportCompany $company): array
    {
        $openingHours = [];
        $dayTranslations = [
            'lundi' => 'monday',
            'mardi' => 'tuesday',
            'mercredi' => 'wednesday',
            'jeudi' => 'thursday',
            'vendredi' => 'friday',
            'samedi' => 'saturday',
            'dimanche' => 'sunday'
        ];

        foreach ($company->getSchedules() as $schedule) {
            $frenchDay = mb_strtolower($schedule->getDayOfWeek());
            $englishDay = $dayTranslations[$frenchDay] ?? $frenchDay;

            $openingHours[] = [
                'day' => $englishDay,
                'open' => $schedule->getOpeningTime()->format('H:i'),
                'close' => $schedule->getClosingTime()->format('H:i'),
            ];
        }

        return $openingHours;
    }

    #[Route('/reservation/{id}', name: 'reservation_details')]
    #[IsGranted('ROLE_USER', subject: 'reservation')]
    public function details(Reservation $reservation): Response
    {
    
        return $this->render('reservation/details.html.twig', [
            'reservation' => $reservation,
            'openingHours' => $reservation->getSportCompany()->getSchedules(),
        ]);
    }
}