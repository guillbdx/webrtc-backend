<?php

namespace App\Form\Type\Pages;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class WithdrawalType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'help' => "Doit être identique à l'adresse email que vous utilisez pour vous connecter au site Dilcam.",
                'data' => $options['email'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email()
                ]
            ])
            ->add('transactionReference', TextType::class, [
                'label' => 'Identifiant de la commande',
                'help' => "Vous trouverez la liste de vos commandes dans votre tableau de bord, rubrique Votre compte.",
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('reason', TextareaType::class, [
                'label' => "Motif de votre rétractation (facultatif)"
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'email' => null
        ]);
    }

}
