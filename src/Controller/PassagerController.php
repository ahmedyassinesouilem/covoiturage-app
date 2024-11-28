<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\CovoiturageCond;
use App\Repository\CovoiturageCondRepository;
use Doctrine\ORM\EntityManagerInterface; // Import correct de EntityManagerInterface
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_PASSAGER')]
#[Route('/passager')]
class PassagerController extends AbstractController
{
    #[Route('/dashboard', name: 'app_passager')]
    public function index(): Response
    {
        return $this->render('passager/index.html.twig', [
            'controller_name' => 'PassagerController',
        ]);
    }

    #[Route('/covoiturageDisponible', name: 'app_covoiturage_disponible')]
    public function afficher(CovoiturageCondRepository $covoiturageRepository): Response
    {
        // Fetch all available covoiturages using the repository
        $covoiturages = $covoiturageRepository->findAll();

        return $this->render('passager/covoiturages_disponibles.html.twig', [
            'covoiturages' => $covoiturages,
        ]);
    }

    #[Route('/reserver/{id}', name: 'app_reservation')]
    public function reserver(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté (passager)
        $passager = $this->getUser();
    
        // Récupérer le covoiturage par son ID
        $repository = $entityManager->getRepository(CovoiturageCond::class);
        $covoiturage = $repository->find($id);
    
        if (!$covoiturage) {
            throw $this->createNotFoundException('Covoiturage non trouvé.');
        }
    
        // Vérifier si une réservation existe déjà pour ce passager
        $existingReservation = $entityManager->getRepository(Reservation::class)->findOneBy([
            'passager' => $passager,
            'covoiturage' => $covoiturage,
        ]);
    
        if ($existingReservation) {
            $this->addFlash('warning', 'Vous avez déjà réservé ce covoiturage.');
            return $this->redirectToRoute('app_covoiturage_disponible');
        }
    
        // Vérifier la disponibilité des places
        if ($covoiturage->isStaurer() || $covoiturage->getNbplace() <= 0) {
            $this->addFlash('error', 'Il n\'y a plus de places disponibles pour ce covoiturage.');
            return $this->redirectToRoute('app_covoiturage_disponible');
        }
    
        // Créer une nouvelle réservation
        $reservation = new Reservation();
        $reservation->setPassager($passager);
        $reservation->setCovoiturage($covoiturage);
        $reservation->setEtat('en_attente'); // État initial : en attente
    
        // Persister dans la base de données
        $entityManager->persist($reservation);
        $entityManager->flush();
    
        // Réduire le nombre de places disponibles
        $entityManager->flush(); // Mettre à jour le covoiturage
    
        // Redirection avec message de succès
        $this->addFlash('success', 'Votre demande de réservation a été envoyée.');
    
        return $this->redirectToRoute('app_covoiturage_disponible');
    }
    
    #[Route('/mes_reservations', name: 'app_mes_reservations')]
    public function mesReservations(EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $passager = $this->getUser();
    
        // Récupérer toutes les réservations de l'utilisateur
        $reservations = $entityManager->getRepository(Reservation::class)->findBy([
            'passager' => $passager,
        ]);
    
        return $this->render('passager/mes_reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }
}
