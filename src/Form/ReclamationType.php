<?php

namespace App\Form;

use App\Entity\Conducteur;
use App\Entity\Passage;
use App\Entity\Reclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objet')
            ->add('conducteur', EntityType::class, [
                'class' => Conducteur::class,
                'choice_label' => 'name', // Afficher le nom du conducteur, mais il sera caché ou désactivé
                'disabled' => true, // Pour rendre le champ non modifiable
                'data' => $options['data']->getConducteur() // Prend automatiquement le conducteur connecté
            ])
            ->add('passager', EntityType::class, [
                'class' => Passage::class,
                'choice_label' => 'name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
