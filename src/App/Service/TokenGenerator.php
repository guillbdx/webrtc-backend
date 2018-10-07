<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Service;

class TokenGenerator
{

    /**
     * @param int $length
     * @param boolean $toUpper
     * @return string
     */
    public function generate($length = 36, $toUpper = false)
    {
        $token = base64_encode(random_bytes(12));
        $token = str_replace(['+', '/'], ['x', 'x'], $token);
        $token = substr($token, 0, $length);
        if ($toUpper) {
            $token = strtoupper($token);
        }
        return $token;
    }
}
