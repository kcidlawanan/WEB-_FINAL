<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ChangePasswordType;

class ChangePasswordController extends AbstractController
{
    #[Route('/change-password', name: 'app_change_password')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();

            $newPassword = $form->get('newPassword')->getData();
            
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $entityManager->flush();

            $this->addFlash('success', 'Password changed successfully');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
