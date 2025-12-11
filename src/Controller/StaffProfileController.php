<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/staff/profile')]
class StaffProfileController extends AbstractController
{
    #[Route('', name: 'staff_profile')]
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_STAFF');
        return $this->render('staff/profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/change-password', name: 'staff_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_STAFF');
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            // Validate CSRF token
            $token = $request->request->get('_token');
            if (!$csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('change_password', $token))) {
                $this->addFlash('error', 'Invalid security token. Please try again.');
                return $this->redirectToRoute('staff_change_password');
            }
            
            $currentPassword = $request->request->get('current_password');
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');
            
            // Check if current password is empty
            if (empty($currentPassword)) {
                $this->addFlash('error', 'Please enter your current password.');
                return $this->redirectToRoute('staff_change_password');
            }
            
            // Validate current password - CRITICAL CHECK
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Current password is incorrect. Please try again.');
                return $this->redirectToRoute('staff_change_password');
            }
            
            // Validate new passwords match
            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'New passwords do not match.');
                return $this->redirectToRoute('staff_change_password');
            }
            
            // Validate new password is not empty and meets minimum length
            if (empty($newPassword) || strlen($newPassword) < 6) {
                $this->addFlash('error', 'New password must be at least 6 characters.');
                return $this->redirectToRoute('staff_change_password');
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $entityManager->flush();

            $this->addFlash('success', 'Password changed successfully!');
            return $this->redirectToRoute('staff_profile');
        }

        return $this->render('staff/profile/change_password.html.twig');
    }
}
