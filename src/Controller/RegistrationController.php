<?php
namespace App\Controller;
use App\Entity\Admin;
use App\Entity\Conducteur;
use App\Entity\Passage;
use App\Form\ConducteurType;
use App\Form\PassagerType;
use App\Form\AdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    
    #[Route("/register/conducteur", name:"register_conducteur")]
    public function registerConducteur(Request $request, EntityManagerInterface $entityManager): Response
    {
        $conducteur = new Conducteur();
        $form = $this->createForm(ConducteurType::class, $conducteur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajouter le rôle pour un conducteur
            $conducteur->setRoles(['ROLE_CONDUCTEUR']);
            
            // Hachage du mot de passe
            $hashedPassword = password_hash($conducteur->getPassword(), PASSWORD_BCRYPT);
            $conducteur->setPassword($hashedPassword);

            // Sauvegarder dans la base de données
            $entityManager->persist($conducteur);
            $entityManager->flush();

            // Afficher un message de succès
            $this->addFlash('success', 'Votre inscription en tant que conducteur a été réussie.');

            // Rediriger l'utilisateur vers la page d'accueil ou autre page
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/conducteur.html.twig', [
            'form' => $form->createView(),
        ]);
    }

   
    #[Route("/register/passager", name:"register_passager")]
    public function registerPassager(Request $request, EntityManagerInterface $entityManager): Response
    {
        $passager = new Passage();
        $form = $this->createForm(PassagerType::class, $passager);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajouter le rôle pour un passager
            $passager->setRoles(['ROLE_PASSAGER']);
            
            // Hachage du mot de passe
            $hashedPassword = password_hash($passager->getPassword(), PASSWORD_BCRYPT);
            $passager->setPassword($hashedPassword);

            // Sauvegarder dans la base de données
            $entityManager->persist($passager);
            $entityManager->flush();

            // Afficher un message de succès
            $this->addFlash('success', 'Votre inscription en tant que passager a été réussie.');

            // Rediriger l'utilisateur vers la page d'accueil ou autre page
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/passager.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route("/register/admin", name:"register_admin")]
    public function registerAdmin(Request $request, EntityManagerInterface $entityManager): Response
    {
        $admin = new Admin();
        $form = $this->createForm(AdminType::class, $admin);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Définir le rôle d'admin
            $admin->setRoles(['ROLE_ADMIN']);
            
            // Hachage du mot de passe
            $hashedPassword = password_hash($admin->getPassword(), PASSWORD_BCRYPT);
            $admin->setPassword($hashedPassword);
    
            // Sauvegarder dans la base de données
            $entityManager->persist($admin);
            $entityManager->flush();
    
            // Afficher un message de succès
            $this->addFlash('success', 'L\'inscription de l\'admin a été réussie.');
    
            // Rediriger l'utilisateur vers la page de connexion ou autre page
            return $this->redirectToRoute('app_login');
        }
    
        return $this->render('registration/admin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

}
