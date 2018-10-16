<?php

namespace App\Form\Type\Dashboard;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Votre mot de passe actuel',
                'mapped' => false,
                'constraints' => [
                    new UserPassword()
                ]
            ])
            ->add('newPassword', RepeatedType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 6
                    ])
                ],
                'type' => PasswordType::class,
                'invalid_message' => "Vous n'avez pas saisi deux fois le même mot de passe",
                'first_options'  => [
                    'label' => 'Nouveau mot de passe',
                    'help' => 'Au moins 6 caractères'
                ],
                'second_options' => array('label' => 'Veuillez répéter le nouveau mot de passe'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
