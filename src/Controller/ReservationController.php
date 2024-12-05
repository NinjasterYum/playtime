<?php

namespace App\Controller;
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

use App\Entity\Reservation;
use App\Entity\SportCompany;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
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

    #[Route('/reservation/new/{id}', name: 'make_reservation', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ReservationValidator $validator, SportCompany $company, ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();

        $reservation = new Reservation();
        $reservation->setSportCompany($company);

        if($user){
            $standardUser = $entityManager->getRepository(StandardUser::class)->find($user->getId());
            if (!$standardUser) {
                $this->addFlash('error', 'Votre compte n\'est pas configuré correctement pour faire une réservation.');
                return $this->redirectToRoute('home');
            }

            $reservation->setStandardUser($standardUser);
        }else{
            if ($request->isMethod('POST')) {
                $form = $this->createForm(ReservationType::class, $reservation, ['company' => $company]);
                $form->handleRequest($request);

                $validationResult = $validator->validate($reservation);

                if ($form->isSubmitted() && $form->isValid()) {
                    if($validationResult->isValid()){
                        $session = $request->getSession();
                        if (!$session) {
                            throw new \RuntimeException('La session n\'est pas disponible');
                        }

                        $session->set('reservation_data', [
                            'reservation' => $request->request->all('reservation'),
                            'sport_company_id' => $company->getId()
                        ]);
                        return $this->redirectToRoute('app_register_user');
                    }else{
                        foreach ($validationResult->getErrors() as $error) {
                            $form->addError(new FormError($error));
                        }
                    }
                }
            }
        }

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

        $allReservations = $reservationRepository->findBy(['sportCompany' => $company]);
        $reservedTimes = [];

        foreach ($allReservations as $existingReservation) {
            $date = $existingReservation->getDate()->format('Y-m-d');
            $time = $existingReservation->getTime()->format('H:i');

            if (!isset($reservedTimes[$date])) {
                $reservedTimes[$date] = [];
            }

            $reservedTimes[$date][] = $time;
        }

        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
            'company' => $company,
            'openingHours' => json_encode($openingHours),
            'reservedTimes' => json_encode($reservedTimes),
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