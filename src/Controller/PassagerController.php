<?php
namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\CovoiturageCond;
use App\Repository\CovoiturageCondRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Entity\Conducteur;
#[IsGranted('ROLE_PASSAGER')]
#[Route('/passager')]
class PassagerController extends AbstractController
{
   // ... dans PassagerController

#[Route('/dashboard', name: 'app_passager')]
public function index(EntityManagerInterface $entityManager): Response
{
    $passager = $this->getUser();
    
    // Récupérer toutes les réservations du passager
    $reservations = $entityManager->getRepository(Reservation::class)->findBy([
        'passager' => $passager
    ], ['id' => 'DESC']);
    
    // Compter les réservations par état
    $validees = 0;
    $enAttente = 0;
    foreach ($reservations as $res) {
        if ($res->getEtat() === 'valide') $validees++;
        elseif ($res->getEtat() === 'en_attente') $enAttente++;
    }
    
    return $this->render('passager/index.html.twig', [
        'reservations' => $reservations,
        'reservationsValidees' => $validees,
        'reservationsEnAttente' => $enAttente,
    ]);
}

    #[Route('/covoiturageDisponible', name: 'app_covoiturage_disponible')]
    public function afficher(CovoiturageCondRepository $covoiturageRepository): Response
    {
        $covoiturages = $covoiturageRepository->findAll();

        return $this->render('passager/covoiturages_disponibles.html.twig', [
            'covoiturages' => $covoiturages,
        ]);
    }

    #[Route('/reserver/{id}', name: 'app_reservation')]
    public function reserver(int $id, EntityManagerInterface $entityManager): Response
    {
        $passager = $this->getUser();
    
        // Récupérer le covoiturage par son ID
        $repository = $entityManager->getRepository(CovoiturageCond::class);
        $covoiturage = $repository->find($id);
    
        if (!$covoiturage) {
            throw $this->createNotFoundException('Covoiturage non trouvé.');
        }
    
        // Vérifier si une réservation existe déjà
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
        $reservation->setEtat('en_attente');
    
        // Persister la réservation
        $entityManager->persist($reservation);
        $entityManager->flush();
    
        // Réduire le nombre de places disponibles
        $covoiturage->setNbplace($covoiturage->getNbplace() - 1);
        $entityManager->flush(); // Mettre à jour le covoiturage
    
        $this->addFlash('success', 'Votre demande de réservation a été envoyée.');
    
        return $this->redirectToRoute('app_covoiturage_disponible');
    }
    
    #[Route('/mes_reservations', name: 'app_mes_reservations')]
    public function mesReservations(EntityManagerInterface $entityManager): Response
    {
        $passager = $this->getUser();
        $reservations = $entityManager->getRepository(Reservation::class)->findBy([
            'passager' => $passager,
        ]);
    
        return $this->render('passager/mes_reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/passager/rechercher', name: 'app_rechercher_covoiturage')]
    public function rechercherCovoiturage(
        CovoiturageCondRepository $covoiturageRepository,
        Request $request
    ): Response {
        // Récupérer les paramètres de recherche
        $villeDepart = $request->query->get('villeDepart');
        $villeArriver = $request->query->get('villeArriver');
        $date = $request->query->get('date'); // Format: 'YYYY-MM-DDTHH:MM'
    
        // Effectuer la recherche dans la base de données
        $queryBuilder = $covoiturageRepository->createQueryBuilder('c');
    
        // Ajouter des conditions de recherche
        if ($villeDepart) {
            $queryBuilder->andWhere('c.villedeDepart LIKE :villeDepart')
                ->setParameter('villeDepart', '%' . $villeDepart . '%');
        }
        if ($villeArriver) {
            $queryBuilder->andWhere('c.villeArriver LIKE :villeArriver')
                ->setParameter('villeArriver', '%' . $villeArriver . '%');
        }
        if ($date) {
            // Convertir la date avec l'heure en objet DateTime
            $dateTime = \DateTime::createFromFormat('Y-m-d\TH:i', $date);
            $queryBuilder->andWhere('c.date >= :date')
                ->setParameter('date', $dateTime);
        }
    
        // Exécuter la requête et récupérer les résultats
        $covoiturages = $queryBuilder->getQuery()->getResult();
    
        return $this->render('passager/covoiturages_disponibles.html.twig', [
            'covoiturages' => $covoiturages,
        ]);
    }
  // src/Controller/PassagerController.php

#[Route('/conducteur/{id}', name: 'app_voir_conducteur')]
public function voirConducteur(int $id, EntityManagerInterface $entityManager): Response
{
    $covoiturage = $entityManager->getRepository(CovoiturageCond::class)->find($id);
    if (!$covoiturage) {
        throw $this->createNotFoundException('Covoiturage introuvable.');
    }

    $conducteur = $covoiturage->getConducteur(); // Récupère le bon conducteur lié à ce covoiturage
    $vehicule = $covoiturage->getVehicule();

    return $this->render('passager/profil_conducteur.html.twig', [
        'conducteur' => $conducteur,
        'vehicule' => $vehicule,
        'covoiturage' => $covoiturage,
    ]);
}

    
}
