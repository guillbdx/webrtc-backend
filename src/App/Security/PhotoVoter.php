<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Security;

use App\Entity\Photo;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PhotoVoter extends Voter
{

    const SHOW = 'show';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (in_array($attribute, [self::SHOW]) === false) {
            return false;
        }
        if ($subject instanceof Photo === false) {
            return false;
        }
        return true;
    }

    /**
     * @param string $attribute
     * @param Photo $photo
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $photo, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();


        if (!$user instanceof User) {
            return false;
        }

        return $photo->getUser()->getId() === $user->getId();
    }

}