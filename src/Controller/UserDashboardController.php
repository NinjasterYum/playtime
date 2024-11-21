<?php

namespace App\Controller;

use App\Entity\StandardUser;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\StandardUserType;

#[IsGranted('ROLE_USER')]
class UserDashboardController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ReservationRepository $reservationRepository;

    public function __construct(EntityManagerInterface $entityManager, ReservationRepository $reservationRepository)
    {
        $this->entityManager = $entityManager;
        $this->reservationRepository = $reservationRepository;
    }

    #[Route('/account', name: 'user_dashboard')]
    public function index(): Response
    {
        /** @var StandardUser $user */
        $user = $this->getUser();
        
        $reservations = $this->reservationRepository->findBy(['standardUser' => $user], ['date' => 'DESC', 'time' => 'DESC']);

        return $this->render('dashboard/user_dashboard.html.twig', [
            'user' => $user,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/account/edit', name: 'user_edit_profile')]
    public function editProfile(Request $request): Response
    {
        /** @var StandardUser $user */
        $user = $this->getUser();

        $form = $this->createForm(StandardUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('user_dashboard');
        }

        return $this->render('dashboard/user_edit_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/account/update', name: 'user_update_profile', methods: ['POST'])]
    public function updateProfile(Request $request): Response
    {
        /** @var StandardUser $user */
        $user = $this->getUser();

        // Ajoutez ici la logique pour mettre à jour le profil de l'utilisateur
        // Utilisez $request->request->get() pour obtenir les données du formulaire

        $this->entityManager->flush();

        $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
        return $this->redirectToRoute('user_dashboard');
    }

    #[Route('/account/reservations', name: 'user_reservations')]
    public function reservations(): Response
    {
        /** @var StandardUser $user */
        $user = $this->getUser();

        $reservations = $this->reservationRepository->findBy(['standardUser' => $user], ['date' => 'DESC', 'time' => 'DESC']);

        return $this->render('dashboard/user_reservations.html.twig', [
            'user' => $user,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/account/reservations/{id}', name: 'user_reservation')]
    public function reservation(Reservation $reservation): Response
    {
        $this->denyAccessUnlessGranted('view', $reservation);

        return $this->render('dashboard/user_reservation.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/account/reservations/{id}/cancel', name: 'user_cancel_reservation', methods: ['POST'])]
    public function cancelReservation(Reservation $reservation): Response
    {
        $this->denyAccessUnlessGranted('edit', $reservation);

        $reservation->setStatus('cancelled');
        $this->entityManager->flush();

        $this->addFlash('success', 'Votre réservation a été annulée avec succès.');
        return $this->redirectToRoute('user_reservations');
    }

    #[Route('/account/reservations/{id}/edit', name: 'user_edit_reservation')]
    public function editReservation(Reservation $reservation): Response
    {
        $this->denyAccessUnlessGranted('edit', $reservation);

        return $this->render('dashboard/user_edit_reservation.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/account/reservations/{id}/update', name: 'user_update_reservation', methods: ['POST'])]
    public function updateReservation(Request $request, Reservation $reservation): Response
    {
        $this->denyAccessUnlessGranted('edit', $reservation);

        // Ajoutez ici la logique pour mettre à jour la réservation
        // Utilisez $request->request->get() pour obtenir les données du formulaire

        $this->entityManager->flush();

        $this->addFlash('success', 'Votre réservation a été mise à jour avec succès.');
        return $this->redirectToRoute('user_reservations');
    }

    #[Route('/account/reservations/{id}/delete', name: 'user_delete_reservation', methods: ['POST'])]
    public function deleteReservation(Reservation $reservation): Response
    {
        $this->denyAccessUnlessGranted('delete', $reservation);

        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        $this->addFlash('success', 'Votre réservation a été supprimée avec succès.');
        return $this->redirectToRoute('user_reservations');
    }
}