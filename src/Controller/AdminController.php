<?php

namespace App\Controller;
use App\Entity\Reclamation;
use App\Entity\Reservation;
use App\Entity\Conducteur;
use App\Entity\Passage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $unvalideconducteur = $entityManager->getRepository(Conducteur::class)->findBy(['valide' => false]);
        return $this->render('admin/index.html.twig', [
            'unvalideconducteur' => $unvalideconducteur,
        ]);
    }

    #[Route('/admin/validate/{id}', name: 'admin_validate_user')]
    public function validateCond(Conducteur $conducteur, EntityManagerInterface $entityManager): Response
    {
        $conducteur->setValide(true);
        $entityManager->flush();
        $this->addFlash('success', 'Conducteur validé avec succès');
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/reservations', name: 'app_admin_reservations')]
    public function viewReservations(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retrieve search parameters (passenger name and driver name)
        $passengerName = $request->query->get('passenger');
        $driverName = $request->query->get('driver');

        // Build the query to search for reservations
        $queryBuilder = $entityManager->getRepository(Reservation::class)->createQueryBuilder('r')
            ->leftJoin('r.passager', 'p')
            ->leftJoin('r.covoiturage', 'c')
            ->leftJoin('c.conducteur', 'd')
            ->where('1 = 1'); // Add a condition that is always true to simplify adding further conditions

        // Apply search filters
        if ($passengerName) {
            $queryBuilder->andWhere('p.name LIKE :passenger')
                ->setParameter('passenger', '%' . $passengerName . '%');
        }

        if ($driverName) {
            $queryBuilder->andWhere('d.name LIKE :driver')
                ->setParameter('driver', '%' . $driverName . '%');
        }

        // Execute the query and get the results
        $reservations = $queryBuilder->getQuery()->getResult();

        return $this->render('admin/reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/admin/reclamations', name: 'app_admin_reclamations')]
    public function viewReclamations(EntityManagerInterface $entityManager): Response
    {
        // Retrieve all reclamations from the database
        $reclamations = $entityManager->getRepository(Reclamation::class)->findAll();

        // Pass the list of reclamations to the Twig template
        return $this->render('admin/reclamations.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }
    #[Route('/admin/users', name: 'app_users')]
    public function afficher(EntityManagerInterface $entityManager): Response
    {
        // Retrieve all users, passagers and conducteurs
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/afficher.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/admin/alert/{id}', name: 'admin_alert_user')]
    public function alertUser(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur par ID
        $user = $entityManager->getRepository(User::class)->find($id);

        if ($user) {
            // Mettre à jour l'alerte (par exemple, augmenter le compteur)
            $user->setAlerte($user->getAlerte() + 1);
            $entityManager->flush();

            $this->addFlash('success', 'Alerte envoyée à ' . $user->getName());
        } else {
            $this->addFlash('error', 'Utilisateur non trouvé');
        }

        return $this->redirectToRoute('app_admin');
    }


}
