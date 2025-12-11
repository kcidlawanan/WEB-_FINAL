<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\ActivityLog;
<<<<<<< HEAD
use App\Entity\Transaction;
=======
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/property')]
final class PropertyController extends AbstractController
{
    #[Route(name: 'app_property_index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository): Response
    {
        return $this->render('property/index.html.twig', [
            'properties' => $propertyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_property_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_STAFF') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** ✅ Handle Image Upload */
           $imageFile = $form->get('image')->getData();

$imageFile = $form->get('image')->getData();

if ($imageFile) {
    $newFilename = uniqid().'.'.$imageFile->guessExtension();
    $imageFile->move(
        $this->getParameter('uploads_directory'),
        $newFilename
    );
    $property->setImage($newFilename);
}


                // assign owner when staff creates a property
                $user = $this->getUser();
                if ($user && method_exists($property, 'setOwner')) {
                    $property->setOwner($user);
                }

                $entityManager->persist($property);
                $entityManager->flush();

                // Log activity
                $user = $this->getUser();
                $log = new ActivityLog();
                $log->setUserId($user?->getId());
                $log->setUsername($user?->getUserIdentifier());
                $roles = $user?->getRoles();
                $log->setRole(is_array($roles) ? implode(',', $roles) : $roles);
                $log->setAction('CREATE');
                $log->setTargetData('Property: ' . $property->getTitle() . ' (ID: ' . $property->getId() . ')');
                $log->setIpAddress($request->getClientIp());
                $entityManager->persist($log);
                $entityManager->flush();

                $this->addFlash('success', 'Property added successfully!');

                // redirect staff to staff property management, admins to admin property management
                if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('app_admin');
                }
                return $this->redirectToRoute('app_staff');
        }

        return $this->render('property/new.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_property_show', methods: ['GET'])]
    public function show(Property $property): Response
    {
        return $this->render('property/show.html.twig', [
            'property' => $property,
        ]);
    }

  #[Route('/{id}/edit', name: 'app_property_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Property $property, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(PropertyType::class, $property);
    $form->handleRequest($request);

        if (!$this->isGranted('ROLE_STAFF') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // enforce ownership: staff can only edit their own properties
        $user = $this->getUser();
        if ($this->isGranted('ROLE_STAFF') && !$this->isGranted('ROLE_ADMIN')) {
            if (method_exists($property, 'getOwner') && $property->getOwner()?->getId() !== $user?->getId()) {
                throw $this->createAccessDeniedException('You are not allowed to edit this record.');
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            // ✅ Delete old image if it exists
            $oldImage = $property->getImage();
            if ($oldImage && file_exists($this->getParameter('uploads_directory') . '/' . $oldImage)) {
                unlink($this->getParameter('uploads_directory') . '/' . $oldImage);
            }

            // ✅ Upload new image
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move(
                $this->getParameter('uploads_directory'),
                $newFilename
            );
            $property->setImage($newFilename);
        }

                $entityManager->flush();

                // Log activity
                $user = $this->getUser();
                $log = new ActivityLog();
                $log->setUserId($user?->getId());
                $log->setUsername($user?->getUserIdentifier());
                $roles = $user?->getRoles();
                $log->setRole(is_array($roles) ? implode(',', $roles) : $roles);
                $log->setAction('UPDATE');
                $log->setTargetData('Property: ' . $property->getTitle() . ' (ID: ' . $property->getId() . ')');
                $log->setIpAddress($request->getClientIp());
                $entityManager->persist($log);
                $entityManager->flush();

                $this->addFlash('success', 'Property updated successfully!');
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_admin');
            }
            return $this->redirectToRoute('app_staff');

    }

    return $this->render('property/edit.html.twig', [
        'property' => $property,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_property_delete', methods: ['POST'])]
    public function delete(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_STAFF') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // ownership check for delete
        $user = $this->getUser();
        if ($this->isGranted('ROLE_STAFF') && !$this->isGranted('ROLE_ADMIN')) {
            if (method_exists($property, 'getOwner') && $property->getOwner()?->getId() !== $user?->getId()) {
                throw $this->createAccessDeniedException('You are not allowed to delete this record.');
            }
        }

        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->request->get('_token'))) {
            // Log activity before deletion
            $user = $this->getUser();
            $log = new ActivityLog();
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
        }
            $this->addFlash('success', 'Property deleted successfully!');
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_admin');
            }
            return $this->redirectToRoute('app_staff');

    }
<<<<<<< HEAD

    #[Route('/{id}/purchase', name: 'app_property_purchase', methods: ['POST'])]
    public function purchase(Property $property, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Check if user is admin or staff
        if (!$this->isGranted('ROLE_STAFF') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // Create transaction record
        $transaction = new Transaction();
        $transaction->setProperty($property);
        $transaction->setBuyer($this->getUser());
        $transaction->setType('purchase');
        $transaction->setAmount((string)$property->getPrice());
        $transaction->setNotes('Purchased by ' . $this->getUser()->getUserIdentifier());

        $entityManager->persist($transaction);

        // Update property status to sold
        $property->setStatus('sold');

        // Log activity
        $user = $this->getUser();
        $log = new ActivityLog();
        $log->setUserId($user?->getId());
        $log->setUsername($user?->getUserIdentifier());
        $roles = $user?->getRoles();
        $log->setRole(is_array($roles) ? implode(',', $roles) : $roles);
        $log->setAction('PURCHASE');
        $log->setTargetData('Property: ' . $property->getTitle() . ' (ID: ' . $property->getId() . ') - Amount: ₱' . $property->getPrice());
        $log->setIpAddress($request->getClientIp());
        $entityManager->persist($log);

        $entityManager->flush();

        $this->addFlash('success', 'Property purchased successfully! Property is now marked as SOLD.');
        return $this->redirectToRoute('app_property_show', ['id' => $property->getId()]);
    }

    #[Route('/{id}/rent', name: 'app_property_rent', methods: ['POST'])]
    public function rent(Property $property, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Check if user is admin or staff
        if (!$this->isGranted('ROLE_STAFF') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // Create transaction record
        $transaction = new Transaction();
        $transaction->setProperty($property);
        $transaction->setBuyer($this->getUser());
        $transaction->setType('rent');
        $transaction->setAmount((string)$property->getPrice());
        $transaction->setNotes('Rented by ' . $this->getUser()->getUserIdentifier());

        $entityManager->persist($transaction);

        // Update property status to rented
        $property->setStatus('rented');

        // Log activity
        $user = $this->getUser();
        $log = new ActivityLog();
        $log->setUserId($user?->getId());
        $log->setUsername($user?->getUserIdentifier());
        $roles = $user?->getRoles();
        $log->setRole(is_array($roles) ? implode(',', $roles) : $roles);
        $log->setAction('RENT');
        $log->setTargetData('Property: ' . $property->getTitle() . ' (ID: ' . $property->getId() . ') - Amount: ₱' . $property->getPrice());
        $log->setIpAddress($request->getClientIp());
        $entityManager->persist($log);

        $entityManager->flush();

        $this->addFlash('success', 'Property rented successfully! Property is now marked as RENTED.');
        return $this->redirectToRoute('app_property_show', ['id' => $property->getId()]);
    }
=======
>>>>>>> 63a58c4601c48fc67eac7ae2ac68cad7aef96129
    }

