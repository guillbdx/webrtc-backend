<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Form\Type\Photo;

use App\Entity\Photo;
use App\Form\DataTransformer\UserToRoomTransformer;
use App\Form\FormEventSubscriber\NormalizePhotoSubscriber;
use App\Validation\Base64JpegImage\Base64JpegImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PhotoType extends AbstractType
{

    /**
     * @var UserToRoomTransformer
     */
    private $userToRoomTransformer;

    /**
     * @param UserToRoomTransformer $userToRoomTransformer
     */
    public function __construct(UserToRoomTransformer $userToRoomTransformer)
    {
        $this->userToRoomTransformer = $userToRoomTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', TextType::class)
            ->add('base64', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Base64JpegImage()
                ]
            ])
        ;

        $builder->get('user')->addModelTransformer($this->userToRoomTransformer);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return '';
    }

}