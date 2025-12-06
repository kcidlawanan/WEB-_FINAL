<?php

namespace App\Controller;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(PropertyRepository $propertyRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'properties' => $propertyRepository->findAll(),
        ]);
    }

    #[Route('/property/delete/{id}', name: 'app_admin_property_delete')]
    public function delete(Property $property, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($property);
        $entityManager->flush();

        $this->addFlash('success', 'Property deleted successfully.');
        return $this->redirectToRoute('app_admin');
    }
}
