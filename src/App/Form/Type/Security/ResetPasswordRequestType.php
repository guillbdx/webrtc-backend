<?php

namespace App\Form\Type\Security;

use App\Form\DataTransformer\UserToEmailTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordRequestType extends AbstractType
{

    /**
     * @var UserToEmailTransformer
     */
    private $userToEmailTransformer;

    /**
     * @param UserToEmailTransformer $userToEmailTransformer
     */
    public function __construct(
        UserToEmailTransformer $userToEmailTransformer
    )
    {
        $this->userToEmailTransformer = $userToEmailTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', TextType::class, [
                'label' => 'Email',
                'invalid_message' => "Cette adresse email n'est pas enregistrée.",
            ])
        ;

        $builder->get('user')->addModelTransformer($this->userToEmailTransformer);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
