<?php

namespace App\Controller;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use App\Repository\ActivityLogRepository;
use App\Entity\User;
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
        // Allow both ROLE_ADMIN and ROLE_STAFF to access
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        // Redirect staff to their dashboard
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_staff');
        }

        return $this->render('admin/index.html.twig', [
            'properties' => $propertyRepository->findAll(),
        ]);
    }

    #[Route('/dashboard', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager, PropertyRepository $propertyRepository, ActivityLogRepository $activityLogRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $userRepo = $entityManager->getRepository(User::class);
        $allUsers = $userRepo->findAll();
        $totalUsers = count($allUsers);

        $totalStaff = 0;
        foreach ($allUsers as $u) {
            if (in_array('ROLE_STAFF', $u->getRoles())) {
                $totalStaff++;
            }
        }

        $totalProperties = $propertyRepository->count([]);

        $recentActivities = $activityLogRepository->findRecent(10);

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalStaff' => $totalStaff,
            'totalProperties' => $totalProperties,
            'recentActivities' => $recentActivities,
        ]);
    }

    #[Route('/property/delete/{id}', name: 'app_admin_property_delete')]
    public function delete(Property $property, EntityManagerInterface $entityManager, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // Log deletion
        $user = $this->getUser();
        $log = new \App\Entity\ActivityLog();
        $log->setUserId($user?->getId());
        $log->setUsername($user?->getUserIdentifier());
        $roles = $user?->getRoles();
        $log->setRole(is_array($roles) ? implode(',', $roles) : $roles);
        $log->setAction('DELETE');
        $log->setTargetData('Property: ' . $property->getTitle() . ' (ID: ' . $property->getId() . ')');
        $log->setIpAddress($request->getClientIp());
        $entityManager->persist($log);

        $entityManager->remove($property);
        $entityManager->flush();

        $this->addFlash('success', 'Property deleted successfully.');
        return $this->redirectToRoute('app_admin');
    }
}
