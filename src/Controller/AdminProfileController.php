<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
<<<<<<< HEAD
use Doctrine\ORM\EntityManagerInterface;
=======
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129

class AdminProfileController extends AbstractController
{
    #[Route('/admin/profile', name: 'admin_profile')]
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/profile.html.twig');
    }

    #[Route('/admin/profile/change-password', name: 'admin_change_password')]
<<<<<<< HEAD
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
=======
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
<<<<<<< HEAD
            $currentPassword = $request->request->get('current_password');
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');
            
            // Validate current password
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Current password is incorrect.');
                return $this->redirectToRoute('admin_change_password');
            }
            
            // Validate new passwords match
            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'New passwords do not match.');
                return $this->redirectToRoute('admin_change_password');
            }
            
            // Validate new password is not empty
            if (empty($newPassword) || strlen($newPassword) < 6) {
                $this->addFlash('error', 'New password must be at least 6 characters.');
                return $this->redirectToRoute('admin_change_password');
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $entityManager->flush();
=======
            $newPassword = $request->request->get('new_password');
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $this->getDoctrine()->getManager()->flush();
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129

            $this->addFlash('success', 'Password changed successfully!');
            return $this->redirectToRoute('admin_profile');
        }

        return $this->render('admin/profile/change_password.html.twig');
    }
}