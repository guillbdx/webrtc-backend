<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Service;


use App\Entity\Photo;

class PhotoNormalizer
{

    public function normalize(Photo $photo): void
    {
        /** We create the gd image */
        $base64 = $photo->getBase64();
        $base64 = str_replace('data:image/jpeg;base64,', '', $base64);
        $blob = base64_decode($base64);
        $gdImage = imagecreatefromstring($blob);

        /** We change the image quality */
        ob_start ();
        imagejpeg ($gdImage, null, 72);
        $blob = ob_get_contents ();
        ob_end_clean ();

        /** We set the new value */
        $base64 = base64_encode($blob);
        $photo->setBase64($base64);
    }

}