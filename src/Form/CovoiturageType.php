<?php

namespace App\Form;

use App\Entity\Conducteur;
use App\Entity\CovoiturageCond;
use App\Entity\Vehicule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CovoiturageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbplace')
            ->add('date')
            ->add('villedeDepart')
            ->add('villeArriver')
            ->add('placedeRencontre')
            ->add('vehicule', EntityType::class, [
                'class' => Vehicule::class,
                'choices' => $options['vehicule_choices'],   // 🔥 restreint aux véhicules passés dans l'option
                'choice_label' => function (Vehicule $vehicule) {
                    return $vehicule->getType() . ' - ' . $vehicule->getModel() . ' (' . $vehicule->getNbpplace() . ' places)';
                },
                'placeholder' => 'Sélectionnez votre véhicule',
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CovoiturageCond::class,
            'vehicule_choices' => [],   // 🔥 nouvelle option par défaut
        ]);
    }
}