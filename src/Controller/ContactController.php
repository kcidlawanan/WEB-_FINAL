<?php

namespace App\Controller;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $contact = new Contact();
            $contact->setName($request->request->get('name'));
            $contact->setEmail($request->request->get('email'));
            $contact->setSubject($request->request->get('subject'));
            $contact->setMessage($request->request->get('message'));

            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Your message has been sent successfully!');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig');
    }

    // --- Minimal admin routes to support admin dashboard links ---
    #[Route('/admin/contact/new', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(): Response
    {
        // For now, redirect to the public contact form (reuse existing view)
        return $this->redirectToRoute('app_contact');
    }

    #[Route('/admin/contact/edit/{id}', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Contact $contact): Response
    {
        // Minimal stub: redirect to public contact form for now.
        // You can expand this to an admin edit form later.
        return $this->redirectToRoute('app_contact');
    }

    #[Route('/admin/contact/delete/{id}', name: 'app_contact_delete', methods: ['POST','GET'])]
    public function delete(Contact $contact, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($contact);
        $entityManager->flush();

        $this->addFlash('success', 'Contact deleted successfully.');
        return $this->redirectToRoute('app_admin');
    }
}
