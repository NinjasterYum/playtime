<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Entity\SportCompany;
use App\Form\SportCompanyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Terrain;
use App\Form\TerrainType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Reservation;
use App\Form\ManualReservationType;
use App\Entity\GuestReservation;
use App\Entity\CompanyImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CompanyDashboardController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/dashboard', name: 'company_dashboard')]
    #[IsGranted('ROLE_COMPANY')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        
        if (!$user instanceof SportCompany) {
            throw $this->createAccessDeniedException('Utilisateur introuvable ou n\'étant pas une société sportive.');
        }

        $guestReservation = new GuestReservation();
        $manualReservationForm = $this->createForm(ManualReservationType::class, $guestReservation, [
            'company' => $user,
        ]);

        return $this->render('dashboard/company_dashboard.html.twig', [
            'company' => $user,
            'manualReservationForm' => $manualReservationForm->createView(),
        ]);
    }


    #[Route('/dashboard/company/update', name: 'company_update')]
#[IsGranted('ROLE_COMPANY')]
public function updateCompany(Request $request): Response
{
    $company = $this->getUser();
    if (!$company instanceof SportCompany) {
        throw $this->createAccessDeniedException('Utilisateur introuvable ou n\'étant pas une société sportive.');
    }

    $form = $this->createForm(SportCompanyType::class, $company);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        try {
            $this->entityManager->flush();
            $this->addFlash('success', 'Les informations de votre entreprise ont été mises à jour avec succès.');
            return $this->redirectToRoute('company_dashboard');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour des informations.');
        }
    }

    return $this->render('dashboard/company_update.html.twig', [
        'form' => $form->createView(),
        'company' => $company,
    ]);
}

    #[Route('/dashboard/services', name: 'company_services')]
    #[IsGranted('ROLE_COMPANY')]
    public function manageServices(): Response
    {
        $company = $this->getUser();
        if (!$company instanceof SportCompany) {
            throw $this->createAccessDeniedException('Utilisateur introuvable ou n\'étant pas une société sportive.');
        }

        return $this->render('dashboard/company_services.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/dashboard/schedule', name: 'company_schedule')]
    #[IsGranted('ROLE_COMPANY')]
    public function manageSchedule(): Response
    {
        $company = $this->getUser();
        if (!$company instanceof SportCompany) {
            throw $this->createAccessDeniedException('Utilisateur introuvable ou n\'étant pas une société sportive.');
        }

        return $this->render('dashboard/company_schedule.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/dashboard/reservations', name: 'company_reservations')]
    #[IsGranted('ROLE_COMPANY')]
    public function showReservations(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof SportCompany) {
            throw $this->createAccessDeniedException('Utilisateur introuvable ou n\'étant pas une société sportive.');
        }

        $reservations = $this->entityManager->getRepository(Schedule::class)->findBy(['sportCompany' => $user]);

        return $this->render('dashboard/company_reservations.html.twig', [
            'company' => $user,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/dashboard/reservation/new', name: 'company_new_reservation', methods: ['POST'])]
    #[IsGranted('ROLE_COMPANY')]
    public function newReservation(Request $request): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user instanceof SportCompany) {
            throw $this->createAccessDeniedException('Utilisateur introuvable ou n\'étant pas une société sportive.');
        }

        $guestReservation = new GuestReservation();
        $form = $this->createForm(ManualReservationType::class, $guestReservation, [
            'company' => $user,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $guestReservation->setSportCompany($user);
            
            $this->entityManager->persist($guestReservation);
            $this->entityManager->flush();

            return new JsonResponse([
                'id' => $guestReservation->getId(),
                'title' => $guestReservation->getService()->getName() . ' - ' . $guestReservation->getClientFirstName() . ' ' . $guestReservation->getClientLastName(),
                'start' => $guestReservation->getDateTime()->format('Y-m-d\TH:i:s'),
                'end' => $guestReservation->getDateTime()->modify('+1 hour')->format('Y-m-d\TH:i:s'),
                'url' => $this->generateUrl('guest_reservation_details', ['id' => $guestReservation->getId()]),
            ]);
        }

        return new JsonResponse(['error' => 'Invalid form submission'], 400);
    }

    #[Route('/dashboard/guest-reservation/{id}', name: 'guest_reservation_details')]
    #[IsGranted('ROLE_COMPANY')]
    public function guestReservationDetails(GuestReservation $guestReservation): Response
    {
        if ($guestReservation->getSportCompany() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette réservation.');
        }

        return $this->render('dashboard/guest_reservation_details.html.twig', [
            'reservation' => $guestReservation,
        ]);
    }

    
    
    #[Route('/dashboard/reservations/calendar', name: 'company_reservations_calendar')]
    #[IsGranted('ROLE_COMPANY')]
    public function getReservationsCalendar(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user instanceof SportCompany) {
            throw $this->createAccessDeniedException('Utilisateur introuvable ou n\'étant pas une société sportive.');
        }
    
        $reservations = $this->entityManager->getRepository(Reservation::class)->findBy(['sportCompany' => $user]);
        $guestReservations = $this->entityManager->getRepository(GuestReservation::class)->findBy(['sportCompany' => $user]);
    
        $events = [];
        foreach ($reservations as $reservation) {
            $events[] = [
                'id' => $reservation->getId(),
                'title' => $reservation->getStandardUser()->getFirstName() . ' ' . $reservation->getStandardUser()->getLastName() . ' - ' . $reservation->getService()->getName(),
                'start' => $reservation->getDateTime()->format('Y-m-d\TH:i:s'),
                'end' => $reservation->getDateTime()->modify('+1 hour')->format('Y-m-d\TH:i:s'),
                'url' => $this->generateUrl('reservation_details', ['id' => $reservation->getId()]),
                'extendedProps' => [
                    'terrainId' => $reservation->getTerrain()->getId(),
                    'status' => $reservation->getStatus(),
                ],
            ];
        }
    
        foreach ($guestReservations as $guestReservation) {
            $events[] = [
                'id' => 'guest_' . $guestReservation->getId(),
                'title' => $guestReservation->getClientFirstName() . ' ' . $guestReservation->getClientLastName() . ' - ' . $guestReservation->getService()->getName(),
                'start' => $guestReservation->getDateTime()->format('Y-m-d\TH:i:s'),
                'end' => $guestReservation->getDateTime()->modify('+1 hour')->format('Y-m-d\TH:i:s'),
                'url' => $this->generateUrl('guest_reservation_details', ['id' => $guestReservation->getId()]),
                'extendedProps' => [
                    'terrainId' => $guestReservation->getTerrain()->getId(),
                    'status' => $guestReservation->getStatus(),
                ],
            ];
        }
    
        return new JsonResponse($events);
    }

    #[Route('/dashboard/terrains', name: 'company_terrains')]
    #[IsGranted('ROLE_COMPANY')]
    public function manageTerrains(Request $request): Response
    {
        $company = $this->getUser();
        if (!$company instanceof SportCompany) {
            throw $this->createAccessDeniedException('Utilisateur introuvable ou n\'étant pas une société sportive.');
        }

        $terrain = new Terrain();
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $terrain->setSportCompany($company);
            $this->entityManager->persist($terrain);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le terrain a été ajouté avec succès.');
            return $this->redirectToRoute('company_terrains');
        }

        return $this->render('dashboard/company_terrains.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/terrain/{id}/edit', name: 'terrain_edit')]
    #[IsGranted('ROLE_COMPANY')]
    public function editTerrain(Request $request, Terrain $terrain): Response
    {
        $company = $this->getUser();
        if (!$company instanceof SportCompany || $terrain->getSportCompany() !== $company) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Le terrain a été mis à jour avec succès.');
            return $this->redirectToRoute('company_terrains');
        }

        return $this->render('dashboard/terrain_edit.html.twig', [
            'form' => $form->createView(),
            'terrain' => $terrain,
        ]);
    }

    #[Route('/dashboard/terrain/{id}/delete', name: 'terrain_delete', methods: ['POST'])]
    #[IsGranted('ROLE_COMPANY')]
    public function deleteTerrain(Request $request, Terrain $terrain): Response
    {
        $company = $this->getUser();
        if (!$company instanceof SportCompany || $terrain->getSportCompany() !== $company) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        if ($this->isCsrfTokenValid('delete'.$terrain->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($terrain);
            $this->entityManager->flush();
            $this->addFlash('success', 'Le terrain a été supprimé avec succès.');
        }

        return $this->redirectToRoute('company_terrains');
    }

    #[Route('/dashboard/guest-reservation/{id}/validate', name: 'validate_guest_reservation')]
    public function validateGuestReservation(GuestReservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($reservation->getSportCompany() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette réservation.');
        }

        $reservation->setStatus('validated');
        $entityManager->flush();

        $this->addFlash('success', 'La réservation a été validée avec succès.');

        return $this->redirectToRoute('guest_reservation_details', ['id' => $reservation->getId()]);
    }

    #[Route('/dashboard/guest-reservation/{id}/cancel', name: 'cancel_guest_reservation')]
    public function cancelGuestReservation(GuestReservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($reservation->getSportCompany() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette réservation.');
        }

        $reservation->setStatus('cancelled');
        $entityManager->flush();

        $this->addFlash('success', 'La réservation a été annulée avec succès.');

        return $this->redirectToRoute('guest_reservation_details', ['id' => $reservation->getId()]);
    }

    #[Route('/upload-company-image', name: 'upload_company_image', methods: ['POST'])]
public function uploadImage(Request $request): JsonResponse
{
    try {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        
        if (!$file) {
            return new JsonResponse(['error' => 'No file uploaded'], 400);
        }

        /** @var SportCompany $company */
        $company = $this->getUser();
        if (!$company) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $companyImage = new CompanyImage();
        $companyImage->setImageFile($file);
        $companyImage->setSportCompany($company);
        
        $this->entityManager->persist($companyImage);
        $this->entityManager->flush();

        // Récupérer l'URL de l'image
        $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/companies/' . $companyImage->getFilename();

        return new JsonResponse([
            'success' => true,
            'id' => $companyImage->getId(),
            'filename' => $companyImage->getFilename(),
            'url' => $imageUrl
        ]);

    } catch (\Exception $e) {
        return new JsonResponse([
            'error' => $e->getMessage()
        ], 500);
    }
}


#[Route('/delete-company-image/{id}', name: 'delete_company_image', methods: ['DELETE'])]
public function deleteImage(CompanyImage $image): JsonResponse
{
    try {
        // Vérifier que l'utilisateur est bien le propriétaire de l'image
        if ($image->getSportCompany() !== $this->getUser()) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à supprimer cette image.');
        }

        // Supprimer l'image
        $this->entityManager->remove($image);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Image supprimée avec succès'
        ]);
    } catch (\Exception $e) {
        return new JsonResponse([
            'success' => false,
            'error' => 'Erreur lors de la suppression de l\'image: ' . $e->getMessage()
        ], 500);
    }
}
}