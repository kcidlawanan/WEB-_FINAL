<?php

namespace App\Controller;

use App\Repository\ActivityLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/logs')]
final class AdminActivityController extends AbstractController
{
    #[Route('/', name: 'admin_activity_logs')]
    public function index(ActivityLogRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $logs = $repo->findRecent(200);

        return $this->render('admin/activity_logs.html.twig', [
            'logs' => $logs,
        ]);
    }
}
