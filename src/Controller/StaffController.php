<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PropertyRepository;

#[Route('/staff')]
final class StaffController extends AbstractController
{
    #[Route('/', name: 'app_staff')]
    public function index(PropertyRepository $propertyRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_STAFF');

        // Staff can see all properties but can only edit their own
        // Admins can see and edit all properties
        $properties = $propertyRepository->findAll();

        return $this->render('staff/index.html.twig', [
            'user' => $this->getUser(),
            'properties' => $properties,
        ]);
    }
}
