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
#[IsGranted('ROLE_CONDUCTEUR')]
#[Route('/conducteur')]
class ConducteurController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/dashboard', name: 'app_conducteur')]
    public function index(): Response
    {
        return $this->render('conducteur/index.html.twig', [
            'controller_name' => 'ConducteurController',
        ]);
    }

    #[Route('/ajout_vehicule', name: 'app_vehicule')]
    public function ajoutVehicule(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté (le conducteur)
        $conducteur = $this->security->getUser();

        // Créer un nouvel objet véhicule
        $vehicule = new Vehicule();
        
        // Associer le conducteur au véhicule
        $vehicule->setConducteur($conducteur);

        // Créer le formulaire
        $form = $this->createForm(VehiculeType::class, $vehicule);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persister le nouveau véhicule dans la base de données
            $entityManager->persist($vehicule);
            $entityManager->flush();

            // Rediriger vers le tableau de bord du conducteur ou une autre page
            return $this->redirectToRoute('app_conducteur');
        }

        // Rendre le formulaire dans le template
        return $this->render('conducteur/ajout_vehicule.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/ajout_covoiturage', name: 'app_covoiturage')]
    public function ajoutCovoiturage(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté (le conducteur)
        $conducteur = $this->security->getUser();
        
        // Créer un nouvel objet CovoiturageCond
        $covoiturage = new CovoiturageCond();
        
        // Associer le conducteur au covoiturage
        $covoiturage->setConducteur($conducteur);
    
        // Créer le formulaire pour le covoiturage
        $form = $this->createForm(CovoiturageType::class, $covoiturage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        // Persister le covoiturage
            $entityManager->persist($covoiturage);
            $entityManager->flush();

        return $this->redirectToRoute('app_conducteur');
        }

    
        // Rendre le formulaire dans le template
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

    
}
