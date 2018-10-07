<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Validation\Base64JpegImage;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class Base64JpegImageValidator extends ConstraintValidator
{

    public function validate($base64, Constraint $constraint)
    {

        if (strlen($base64) <= 23) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
            return;
        }

        if ('data:image/jpeg;base64,' !== substr($base64, 0, 23)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }


    }

}