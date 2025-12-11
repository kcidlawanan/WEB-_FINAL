<?php

namespace App\Controller;

use App\Repository\ActivityLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
<<<<<<< HEAD
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
=======
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129

#[Route('/admin/logs')]
final class AdminActivityController extends AbstractController
{
    #[Route('/', name: 'admin_activity_logs')]
    public function index(ActivityLogRepository $repo): Response
    {
<<<<<<< HEAD
        // Only ROLE_ADMIN can access activity logs
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. Only administrators can view activity logs.');
        }
=======
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129

        $logs = $repo->findRecent(200);

        return $this->render('admin/activity_logs.html.twig', [
            'logs' => $logs,
        ]);
    }
}
