<?php

namespace App\Controller;

use App\Entity\Property;
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


            $entityManager->persist($property);
            $entityManager->flush();

            $this->addFlash('success', 'Property added successfully!');

           return $this->redirectToRoute('app_admin');
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

        $this->addFlash('success', 'Property updated successfully!');
      return $this->redirectToRoute('app_admin');

    }

    return $this->render('property/edit.html.twig', [
        'property' => $property,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_property_delete', methods: ['POST'])]
    public function delete(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($property);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Property deleted successfully!');
        return $this->redirectToRoute('app_admin');

    }
}
