<?php
namespace App\Controller;

use App\Entity\Vehicule;
use App\Entity\CovoiturageCond;
use App\Entity\Reservation;
use App\Form\VehiculeType;
use App\Form\CovoiturageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\CovoiturageCondRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[IsGranted('ROLE_CONDUCTEUR')]
#[Route('/conducteur')]
class ConducteurController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

   // ... dans ConducteurController

#[Route('/dashboard', name: 'app_conducteur')]
public function index(EntityManagerInterface $entityManager): Response
{
    $conducteur = $this->getUser();
    
    // Covoiturages du conducteur
    $covoiturages = $entityManager->getRepository(CovoiturageCond::class)->findBy([
        'conducteur' => $conducteur
    ]);
    
    // Statistiques
    $totalCovoiturages = count($covoiturages);
    $placesRestantes = 0;
    $totalReservationsRecues = 0;
    $reservationsEnAttente = 0;
    
    foreach ($covoiturages as $c) {
        $placesRestantes += $c->getNbplace();
        $totalReservationsRecues += count($c->getReservations());
        foreach ($c->getReservations() as $res) {
            if ($res->getEtat() === 'en_attente') $reservationsEnAttente++;
        }
    }
    
    // Prochains trajets (date >= aujourd'hui)
    $now = new \DateTime();
    $prochainsTrajets = array_filter($covoiturages, function($c) use ($now) {
        return $c->getDate() >= $now;
    });
    usort($prochainsTrajets, fn($a,$b) => $a->getDate() <=> $b->getDate());
    
    // Dernières réservations (limite 5)
    $dernieresReservations = $entityManager->getRepository(Reservation::class)->createQueryBuilder('r')
        ->join('r.covoiturage', 'c')
        ->where('c.conducteur = :conducteur')
        ->setParameter('conducteur', $conducteur)
        ->orderBy('r.id', 'DESC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();
    
    return $this->render('conducteur/index.html.twig', [
        'totalCovoiturages' => $totalCovoiturages,
        'totalReservationsRecues' => $totalReservationsRecues,
        'placesRestantes' => $placesRestantes,
        'reservationsEnAttente' => $reservationsEnAttente,
        'prochainsTrajets' => $prochainsTrajets,
        'dernieresReservations' => $dernieresReservations,
    ]);
}


#[Route('/ajout_vehicule', name: 'app_vehicule')]
public function ajoutVehicule(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $conducteur = $this->getUser();
    $vehicule = new Vehicule();
    $vehicule->setConducteur($conducteur);
    $form = $this->createForm(VehiculeType::class, $vehicule);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageFile')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
            try {
                $imageFile->move(
                    $this->getParameter('vehicules_directory'),
                    $newFilename
                );
                $vehicule->setImageFilename($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
            }
        }
        $entityManager->persist($vehicule);
        $entityManager->flush();
        $this->addFlash('success', 'Véhicule ajouté avec succès.');
        return $this->redirectToRoute('app_mes_vehicules');
    }

    return $this->render('conducteur/ajout_vehicule.html.twig', [
        'form' => $form->createView(),
    ]);
}
    #[Route('/ajout_covoiturage', name: 'app_covoiturage')]
public function ajoutCovoiturage(Request $request, EntityManagerInterface $entityManager): Response
{
    $conducteur = $this->security->getUser();
    
    // Récupérer uniquement les véhicules de ce conducteur
    $vehicules = $entityManager->getRepository(Vehicule::class)->findBy(['conducteur' => $conducteur]);

    $covoiturage = new CovoiturageCond();
    $covoiturage->setConducteur($conducteur);

    // Passer la liste des véhicules en option du formulaire
    $form = $this->createForm(CovoiturageType::class, $covoiturage, [
        'vehicule_choices' => $vehicules,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($covoiturage);
        $entityManager->flush();
        $this->addFlash('success', 'Covoiturage ajouté avec succès.');
        return $this->redirectToRoute('app_conducteur');
    }

    return $this->render('conducteur/ajout_covoiturage.html.twig', [
        'form' => $form->createView(),
    ]);
}
    #[Route('/mes_covoiturages', name: 'app_mes_covoiturages')]
    public function mesCovoiturages(EntityManagerInterface $entityManager): Response
    {
         // Récupérer l'utilisateur connecté (le conducteur)
        $conducteur = $this->security->getUser();

    // Rechercher les covoiturages créés par ce conducteur
        $repository = $entityManager->getRepository(CovoiturageCond::class);
        $mesCovoiturages = $repository->findBy(['conducteur' => $conducteur]);

    // Afficher les covoiturages dans le template
        return $this->render('conducteur/mes_covoiturages.html.twig', [
            'covoiturages' => $mesCovoiturages,
        ]);
    }
    #[Route('/modifier_covoiturage/{id}', name: 'app_covoiturage_modifier')]
public function modifierCovoiturage(int $id,Request $request,EntityManagerInterface $entityManager): Response 
    {
        $conducteur = $this->security->getUser();

      // Récupérer le covoiturage par son ID
       $repository = $entityManager->getRepository(CovoiturageCond::class);
       $covoiturage = $repository->find($id);

    // Vérifier que le covoiturage appartient bien au conducteur connecté
     if (!$covoiturage || $covoiturage->getConducteur() !== $conducteur) {
        throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce covoiturage.');
      }

    // Créer le formulaire et gérer la soumission
      $form = $this->createForm(CovoiturageType::class, $covoiturage);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_mes_covoiturages');
       }

       return $this->render('conducteur/modifier_covoiturage.html.twig', [
          'form' => $form->createView(),
       ]);
    }
    #[Route('/suprimer_covoiturage/{id}', name: 'app_covoiturage_suprimer')]
    public function suprimer(
        $id, 
        CovoiturageCondRepository $covoiturageRepository, 
        EntityManagerInterface $entityManager
    ): Response {
        $covoiturage = $covoiturageRepository->find($id);
    
        if (!$covoiturage) {
            throw $this->createNotFoundException('Covoiturage non trouvé.');
        }
    
        // Supprimer les réservations liées
        foreach ($covoiturage->getReservations() as $reservation) {
            $entityManager->remove($reservation);
        }
    
        // Supprimer le covoiturage
        $entityManager->remove($covoiturage);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_mes_covoiturages');
    }
    
    #[Route('/reservations', name: 'app_conducteur_reservations')]
    public function afficherReservations(EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté (conducteur)
        $conducteur = $this->getUser();

        // Rechercher les réservations pour ses covoiturages
        $repository = $entityManager->getRepository(Reservation::class);
        $reservations = $repository->createQueryBuilder('r')
            ->join('r.covoiturage', 'c')
            ->where('c.conducteur = :conducteur')
            ->setParameter('conducteur', $conducteur)
            ->getQuery()
            ->getResult();

        // Afficher les réservations dans le template
        return $this->render('conducteur/reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }
    #[Route('/reservation/{id}/valider', name: 'app_reservation_valider')]
    public function validerReservation(int $id, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Reservation::class);
        $reservation = $repository->find($id);
    
        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée.');
        }
    
        // Modifier l'état de la réservation
        $reservation->setEtat('valide');
    
        // Récupérer le covoiturage lié
        $covoiturage = $reservation->getCovoiturage();
    
        if ($covoiturage) {
            // Réduire le nombre de places disponibles
            $covoiturage->decrementNbplace();
    
            // Vérifier si le covoiturage est saturé
            if ($covoiturage->getNbplace() === 0) {
                $covoiturage->setStaurer(true);
            }
        }
    
        $entityManager->flush();
    
        $this->addFlash('success', 'La réservation a été validée.');
    
        return $this->redirectToRoute('app_conducteur_reservations');
    }
    

    #[Route('/reservation/{id}/refuser', name: 'app_reservation_refuser')]
    public function refuserReservation(int $id, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Reservation::class);
        $reservation = $repository->find($id);

        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée.');
        }

        // Modifier l'état de la réservation
        $reservation->setEtat('refuse');
        $entityManager->flush();

        $this->addFlash('danger', 'La réservation a été refusée.');

        return $this->redirectToRoute('app_conducteur_reservations');
    }
    #[Route('/reclamation', name: 'app_conducteur_reclamation')]
public function creerReclamation(Request $request, EntityManagerInterface $entityManager): Response
{
    $conducteur = $this->getUser();
    $reclamation = new Reclamation();
    $reclamation->setConducteur($conducteur);

    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $this->addFlash('success', 'Votre réclamation a été envoyée.');
        return $this->redirectToRoute('app_conducteur');
    }

    return $this->render('conducteur/reclamation.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route('/mes_vehicules', name: 'app_mes_vehicules')]
public function mesVehicules(EntityManagerInterface $entityManager): Response
{
    $conducteur = $this->getUser();
    $vehicules = $conducteur->getVehicules(); 

    return $this->render('conducteur/mes_vehicules.html.twig', [
        'vehicules' => $vehicules,
    ]);
}

#[Route('/vehicule/supprimer/{id}', name: 'app_vehicule_supprimer')]
public function supprimerVehicule(int $id, EntityManagerInterface $entityManager): Response
{
    $conducteur = $this->getUser();
    $vehicule = $entityManager->getRepository(Vehicule::class)->find($id);

    if (!$vehicule || $vehicule->getConducteur() !== $conducteur) {
        throw $this->createAccessDeniedException();
    }

    // Supprimer le fichier image s’il existe
    if ($vehicule->getImageFilename()) {
        $imagePath = $this->getParameter('vehicules_directory') . '/' . $vehicule->getImageFilename();
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $entityManager->remove($vehicule);
    $entityManager->flush();
    $this->addFlash('success', 'Véhicule supprimé.');

    return $this->redirectToRoute('app_mes_vehicules');
}
    
}
