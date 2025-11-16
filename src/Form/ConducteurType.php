<?php

namespace App\Form;

use App\Entity\Conducteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
class ConducteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('date_naissance' , DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date'
        ]) 
            ->add('name' ,TextType::class,['label'=>'Name'] )
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('password', PasswordType::class, ['label' => 'Mot de passe'])
            ->add('numPermis', TextType::class, ['label' => 'Numéro de permis'])
            ->add('adress', TextType::class, ['label' => 'Adresse']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conducteur::class,
        ]);
    }
}

