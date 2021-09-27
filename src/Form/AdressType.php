<?php

namespace App\Form;

use App\Entity\Adress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'adresse',
                'attr' => ['placeholder' => 'Entrer un nom pour votre adresse : exemple Maison, Travail,...']
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Entrer votre prénom']
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Entrer votre nom']
            ])
            ->add('company', TextType::class, [
                'label' => 'Entreprise',
                'required' => false,
                'attr' => ['placeholder' => '(facultatif) Renseigner le nom de votre entreprise']
            ])
            ->add('adress', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['placeholder' => 'Entrer le numeros de rue et le nom de la rue']
            ])
            ->add('postal', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['placeholder' => 'Entrer votre code postal']
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => ['placeholder' => 'Entrer votre ville']
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'attr' => ['placeholder' => 'Entrer votre pays de résidence']
            ])
            ->add('phone', TelType::class, [
                'label' => 'téléphone',
                'attr' => ['placeholder' => 'Entrer votre numéros de téléphone']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}
