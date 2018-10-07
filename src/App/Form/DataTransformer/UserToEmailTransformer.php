<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Form\DataTransformer;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UserToEmailTransformer implements DataTransformerInterface
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserToEmailTransformer constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param mixed $user
     * @return mixed|string|User
     */
    public function transform($user)
    {
        if (null == $user) {
            return '';
        }

        return $user->getEmail();
    }

    /**
     * @param mixed $email
     * @return User|mixed|null
     */
    public function reverseTransform($email)
    {
        if (!$email) {
            throw new TransformationFailedException();
        }
        $user = $this->userRepository->findOneBy([
            'email' => $email
        ]);
        if (false === $user instanceof User) {
            throw new TransformationFailedException();
        }

        return $user;
    }

}