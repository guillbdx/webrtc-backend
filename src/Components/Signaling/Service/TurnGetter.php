<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace Components\Signaling\Service;

class TurnGetter
{

    /**
     * @return array
     */
    public function getTurnServers()
    {
        $credentials = $this->generateCredentials();

        return [
            [
                'urls'          => ['turn:turn.dilcam.com'],
                'username'      => $credentials['username'],
                'credential'    => $credentials['credential']
            ]
            ,
            [
                'urls'          => 'turn:turn1.dilcam.com:443',
                'username'      => $credentials['username'],
                'credential'    => $credentials['credential']
            ]
        ];
    }

    /**
     * @return array
     */
    private function generateCredentials(): array
    {
        $unixTimeStamp = time() + 180;
        $username = $unixTimeStamp.':dilcam';
        $credential = base64_encode(hash_hmac('sha1', $username, 'j4qhGfd8qO2kn5GsH', true));

        return [
            'username' => $username,
            'credential' => $credential
        ];
    }

}