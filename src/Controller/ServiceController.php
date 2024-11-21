<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Service;
use App\Form\ServiceType;
use Doctrine\ORM\EntityManagerInterface;

class ServiceController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/dashboard/services', name: 'company_services')]
    public function manageServices(Request $request): Response
    {
        $company = $this->getUser();
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->setSportCompany($company);
            $this->entityManager->persist($service);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Nouveau service ajouté avec succès.');
            return $this->redirectToRoute('company_services');
        }
        
        return $this->render('dashboard/company_services.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/services/edit/{id}', name: 'service_edit')]

    public function editService(Request $request): Response
    {
        $service = $this->getUser();
        $form = $this->createForm(ServiceType::class, $service);
        $company = $this->getUser();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($service);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Service modifié avec succès.');
            return $this->redirectToRoute('company_services');
        }
        
        return $this->render('dashboard/company_services.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/services/delete/{id}', name: 'service_delete')]

    public function deleteService(Service $service): Response
    {
        $this->entityManager->remove($service);
        $this->entityManager->flush();
        
        $this->addFlash('success', 'Service supprimé avec succès.');
        return $this->redirectToRoute('company_services');
    }

    
}
