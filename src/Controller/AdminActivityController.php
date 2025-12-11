<?php

namespace App\Controller;

use App\Repository\ActivityLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/admin/logs')]
final class AdminActivityController extends AbstractController
{
    #[Route('/', name: 'admin_activity_logs')]
    public function index(ActivityLogRepository $repo): Response
    {
        // Only ROLE_ADMIN can access activity logs
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. Only administrators can view activity logs.');
        }

        $logs = $repo->findRecent(200);

        return $this->render('admin/activity_logs.html.twig', [
            'logs' => $logs,
        ]);
    }
}
