<?php

namespace App\Form\Type\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', null, [
                'label' => 'Email',
                'data' => $options['lastUsername']
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'Mot de passe'
            ])
            ->add('_remember_me', CheckboxType::class, [
                'label' => 'Se souvenir de moi'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'lastUsername' => null
        ]);
    }

    public function getBlockPrefix()
    {
        return null;
    }
}
