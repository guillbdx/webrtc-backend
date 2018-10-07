<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Validation\Base64JpegImage;

use Symfony\Component\Validator\Constraint;

class Base64JpegImage extends Constraint
{

    public $message = "Cette chaîne de caractères ne représente pas une image JPEG en base 64.";

}