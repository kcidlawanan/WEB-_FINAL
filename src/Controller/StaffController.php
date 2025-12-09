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

        // Staff should see only their own properties; admins see all
        $user = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN')) {
            $properties = $propertyRepository->findAll();
        } else {
            $properties = $propertyRepository->findBy(['owner' => $user]);
        }

        return $this->render('staff/index.html.twig', [
            'user' => $user,
            'properties' => $properties,
        ]);
    }
}
