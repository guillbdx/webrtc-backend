<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Form\DataTransformer;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UserToRoomTransformer implements DataTransformerInterface
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
     * @param mixed $user|User
     * @return mixed|string
     */
    public function transform($user)
    {
        if (null == $user) {
            return '';
        }

        return $user->getRoom();
    }

    /**
     * @param mixed|string $room
     * @return User|mixed|null
     */
    public function reverseTransform($room)
    {
        if (!$room) {
            throw new TransformationFailedException();
        }
        $user = $this->userRepository->findOneBy([
            'roomId' => $room
        ]);
        if (false === $user instanceof User) {
            throw new TransformationFailedException();
        }

        return $user;
    }

}