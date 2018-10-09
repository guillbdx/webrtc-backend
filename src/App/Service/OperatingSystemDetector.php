<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 10/9/18
 * Time: 11:48 PM
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class OperatingSystemDetector
{

    public const WINDOWS    = 'WINDOWS';

    public const MAC        = 'MAC';

    public const OTHER      = 'OTHER';

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(
        RequestStack $requestStack
    )
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return string
     */
    public function detect(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $server = $request->server;
        if (false === $server->has('HTTP_USER_AGENT')) {
            return self::OTHER;
        }
        $userAgent = $server->get('HTTP_USER_AGENT');

        if (preg_match('/Mac/i', $userAgent)) {
            return self::MAC;
        }
        if (preg_match('/Windows/i', $userAgent)) {
            return self::WINDOWS;
        }

        return self::OTHER;
    }

}