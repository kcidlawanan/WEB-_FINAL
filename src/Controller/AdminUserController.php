<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ActivityLog;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
<<<<<<< HEAD
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
=======
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129

class AdminUserController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_users')]
    public function index(EntityManagerInterface $entityManager): Response
    {
<<<<<<< HEAD
        // Only ROLE_ADMIN can access user management
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. Only administrators can manage users.');
        }
        
=======
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/new', name: 'admin_users_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
<<<<<<< HEAD
        // Only ROLE_ADMIN can create users
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. Only administrators can create user accounts.');
        }
        
=======
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the plain password before persisting
            if ($user->getPassword()) {
                $hashed = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashed);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            // Log user creation
            $adminUser = $this->getUser();
            $log = new ActivityLog();
            $log->setUserId($adminUser?->getId());
            $log->setUsername($adminUser?->getUserIdentifier());
            $roles = $adminUser?->getRoles();
            $log->setRole(is_array($roles) ? implode(',', $roles) : $roles);
            $log->setAction('CREATE');
            $log->setTargetData('User: ' . $user->getEmail() . ' (ID: ' . $user->getId() . ', Roles: ' . implode(',', $user->getRoles()) . ')');
            $log->setIpAddress($request->getClientIp());
            $entityManager->persist($log);
            $entityManager->flush();

            $this->addFlash('success', 'User account created successfully!');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/edit/{id}', name: 'admin_users_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
<<<<<<< HEAD
        // Only ROLE_ADMIN can edit users
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. Only administrators can edit user accounts.');
        }
        
        // Store original roles to prevent unauthorized role changes
        $originalRoles = $user->getRoles();
        
=======
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
<<<<<<< HEAD
            // Verify that roles weren't changed by staff trying to bypass
            $newRoles = $user->getRoles();
            if ($newRoles !== $originalRoles) {
                // Log unauthorized role change attempt
                $adminUser = $this->getUser();
                $log = new ActivityLog();
                $log->setUserId($adminUser?->getId());
                $log->setUsername($adminUser?->getUserIdentifier());
                $roles = $adminUser?->getRoles();
                $log->setRole(is_array($roles) ? implode(',', $roles) : $roles);
                $log->setAction('UNAUTHORIZED_ATTEMPT');
                $log->setTargetData('Attempted unauthorized role change for User: ' . $user->getEmail());
                $log->setIpAddress($request->getClientIp());
                $entityManager->persist($log);
                $entityManager->flush();
                
                throw new AccessDeniedException('Unauthorized attempt to change user roles.');
            }
            
=======
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129
            // If password was changed in the form, hash it before saving
            $plain = $form->get('password')->getData();
            if ($plain) {
                $hashed = $passwordHasher->hashPassword($user, $plain);
                $user->setPassword($hashed);
            }

            $entityManager->flush();

            // Log user update
            $adminUser = $this->getUser();
            $log = new ActivityLog();
            $log->setUserId($adminUser?->getId());
            $log->setUsername($adminUser?->getUserIdentifier());
            $roles = $adminUser?->getRoles();
            $log->setRole(is_array($roles) ? implode(',', $roles) : $roles);
            $log->setAction('UPDATE');
            $log->setTargetData('User: ' . $user->getEmail() . ' (ID: ' . $user->getId() . ', Roles: ' . implode(',', $user->getRoles()) . ')');
            $log->setIpAddress($request->getClientIp());
            $entityManager->persist($log);
            $entityManager->flush();

            $this->addFlash('success', 'User account updated successfully!');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/delete/{id}', name: 'admin_users_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
<<<<<<< HEAD
        // Only ROLE_ADMIN can delete users
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. Only administrators can delete user accounts.');
        }
        
=======
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Log user deletion before removing
            $adminUser = $this->getUser();
            $log = new ActivityLog();
            $log->setUserId($adminUser?->getId());
            $log->setUsername($adminUser?->getUserIdentifier());
            $roles = $adminUser?->getRoles();
            $log->setRole(is_array($roles) ? implode(',', $roles) : $roles);
            $log->setAction('DELETE');
            $log->setTargetData('User: ' . $user->getEmail() . ' (ID: ' . $user->getId() . ')');
            $log->setIpAddress($request->getClientIp());
            $entityManager->persist($log);

            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'User account deleted successfully!');
        }

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/admin/users/disable/{id}', name: 'admin_users_disable', methods: ['POST'])]
    public function disable(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
<<<<<<< HEAD
        // Only ROLE_ADMIN can disable users
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. Only administrators can disable user accounts.');
        }
=======
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129
        
        // Toggle the active status
        $wasActive = $user->isActive();
        $user->setIsActive(!$user->isActive());
        $entityManager->flush();

        // Log user status change
        $adminUser = $this->getUser();
        $log = new ActivityLog();
        $log->setUserId($adminUser?->getId());
        $log->setUsername($adminUser?->getUserIdentifier());
        $roles = $adminUser?->getRoles();
        $log->setRole(is_array($roles) ? implode(',', $roles) : $roles);
        $log->setAction('UPDATE');
        $action = $user->isActive() ? 'ENABLED' : 'DISABLED';
        $log->setTargetData('User: ' . $user->getEmail() . ' (ID: ' . $user->getId() . ') - Status ' . $action);
        $log->setIpAddress($request->getClientIp());
        $entityManager->persist($log);
        $entityManager->flush();

        $message = $user->isActive() ? 'User account enabled successfully!' : 'User account disabled successfully!';
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/admin/users/create', name: 'admin_users_create')]
    public function createUser(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
<<<<<<< HEAD
        // Only ROLE_ADMIN can create users
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. Only administrators can create user accounts.');
        }
        
=======
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'User created successfully!');

            return $this->redirectToRoute('admin_users_list');
        }

        return $this->render('admin/users/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users', name: 'admin_users_list')]
    public function listUsers(EntityManagerInterface $entityManager): Response
    {
<<<<<<< HEAD
        // Only ROLE_ADMIN can list users
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied. Only administrators can view user accounts.');
        }
        
=======
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/users/list.html.twig', [
            'users' => $users,
        ]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129
