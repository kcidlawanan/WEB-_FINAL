<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminProfileController extends AbstractController
{
    #[Route('/admin/profile', name: 'admin_profile')]
    public function profile(): Response
    {
        return $this->render('admin/profile/view.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/admin/profile/change-password', name: 'admin_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('new_password');
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Password changed successfully!');
            return $this->redirectToRoute('admin_profile');
        }

        return $this->render('admin/profile/change_password.html.twig');
    }
}