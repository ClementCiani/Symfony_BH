<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, [
                'label' => 'Entrer votre prénom',
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Entrer votre nom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Entrer votre Email',
            ])
            ->add('objet', TextType::class, [
                'label' => 'Entrer l\'objet de votre message',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Entrer votre message',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
