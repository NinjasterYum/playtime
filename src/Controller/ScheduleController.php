<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Entity\SportCompany;
use App\Form\ScheduleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;

class ScheduleController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/dashboard/schedule', name: 'company_schedule')]
    public function manageSchedule(Request $request): Response
    {
        $company = $this->getUser();
        if (!$company instanceof SportCompany) {
            throw $this->createAccessDeniedException('Vous devez être connecté en tant qu\'entreprise.');
        }

        $schedule = new Schedule();
        $form = $this->createForm(ScheduleType::class, $schedule);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $schedule->setSportCompany($company);
            $this->entityManager->persist($schedule);
            $this->entityManager->flush();

            $this->addFlash('success', 'Nouvel horaire ajouté avec succès.');
            return $this->redirectToRoute('company_schedule');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
                $this->logger->error('Form error', ['message' => $error->getMessage()]);
            }
        }
        
        return $this->render('dashboard/company_schedule.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/schedule/new/{id}', name: 'schedule_new')]
    #[IsGranted('ROLE_COMPANY')]
    public function addSchedule(Request $request, EntityManagerInterface $entityManager, SportCompany $sportCompany): Response
    {
        $schedule = new Schedule();
        $schedule->setSportCompany($sportCompany);

        $form = $this->createForm(ScheduleType::class, $schedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($schedule);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvel horaire ajouté avec succès.');
            return $this->redirectToRoute('company_dashboard');
        }

        return $this->render('schedule/new.html.twig', [
            'form' => $form->createView(),
            'sportCompany' => $sportCompany,
        ]);
    }

    #[Route('/schedule/{id}/edit', name: 'schedule_edit')]
    #[IsGranted('ROLE_COMPANY')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Schedule $schedule): Response
    {
        if ($schedule->getSportCompany() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet horaire.');
        }

        $form = $this->createForm(ScheduleType::class, $schedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Horaire mis à jour avec succès.');
            return $this->redirectToRoute('company_dashboard');
        }

        return $this->render('schedule/edit.html.twig', [
            'form' => $form->createView(),
            'schedule' => $schedule,
        ]);
    }

    #[Route('/schedule/{id}/delete', name: 'schedule_delete')]
    #[IsGranted('ROLE_COMPANY')]
    public function delete(Request $request, EntityManagerInterface $entityManager, Schedule $schedule): Response
    {
        if ($schedule->getSportCompany() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cet horaire.');
        }

        if ($this->isCsrfTokenValid('delete'.$schedule->getId(), $request->request->get('_token'))) {
            $entityManager->remove($schedule);
            $entityManager->flush();

            $this->addFlash('success', 'Horaire supprimé avec succès.');
        }

        return $this->redirectToRoute('company_dashboard');
    }
}