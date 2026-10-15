<?php

namespace App\Form;

use App\Entity\Conducteur;
use App\Entity\Vehicule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Clio, 208, Golf...']
            ])
            ->add('nbpplace', IntegerType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => '4, 5, 7...']
            ])
            ->add('model', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Renault, Peugeot...']
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Photo du véhicule',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control', 'accept' => 'image/jpeg,image/png,image/webp']
            ]);
    }
 
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
