<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Factory;

use App\Entity\Photo;
use DateTimeImmutable;

class PhotoFactory
{

    /**
     * @return Photo
     */
    public function init(): Photo
    {
        $photo = new Photo();
        $photo->setCreatedAt(new DateTimeImmutable());

        return $photo;
    }

}