<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation/new', name: 'reclamation_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();

        // Créez le formulaire pour la réclamation
        $form = $this->createForm(ReclamationType::class, $reclamation);

        // Récupérez l'utilisateur connecté
        $user = $this->getUser();

        // Si l'utilisateur est un conducteur, définissez-le automatiquement dans le formulaire
        if ($user instanceof \App\Entity\Conducteur) {
            $reclamation->setConducteur($user);
            // Désactivez ou masquez le champ conducteur si nécessaire
            $form->get('conducteur')->setData($user);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrez la réclamation dans la base de données
            $entityManager->persist($reclamation);
            $entityManager->flush();

            // Redirige vers la page de succès
            return $this->redirectToRoute('reclamation_success');
        }

        return $this->render('reclamation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reclamation/success', name: 'reclamation_success')]
    public function success(): Response
    {
        return $this->render('reclamation/success.html.twig');
    }
}
