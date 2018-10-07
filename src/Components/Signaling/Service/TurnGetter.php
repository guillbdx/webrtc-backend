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
                'urls'           => 'turn:turn.illicam.com',
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
        $secret = 'eRoN9plAw6v1B';
        $username = gmdate('Y-m-d-H-i', time()-60);
        $plainPassword = $username.$secret;
        $credential = md5($plainPassword);

        return [
            'username' => $username,
            'credential' => $credential
        ];
    }

}