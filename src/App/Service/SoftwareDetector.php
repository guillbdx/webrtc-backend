<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 10/9/18
 * Time: 11:48 PM
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class SoftwareDetector
{

    public const WINDOWS            = 'WINDOWS';
    public const MAC                = 'MAC';
    public const LINUX              = 'LINUX';
    public const ANDROID            = 'ANDROID';
    public const IPHONE             = 'IPHONE';

    public const CHROME             = 'CHROME';
    public const FIREFOX            = 'FIREFOX';
    public const SAFARI             = 'SAFARI';
    public const EDGE_IE_TRIDENT    = 'EDGE_IE_TRIDENT';
    public const OPERA              = 'OPERA';

    public const OTHER              = 'OTHER';

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
    public function detectOperatingSystem(): string
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
        if (preg_match('/Droid/i', $userAgent)) {
            return self::ANDROID;
        }
        if (preg_match('/iPhone/i', $userAgent)) {
            return self::IPHONE;
        }
        if (preg_match('/Linux/i', $userAgent)) {
            return self::LINUX;
        }

        return self::OTHER;
    }

    /**
     * @return string
     */
    public function detectBrowser(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $server = $request->server;
        if (false === $server->has('HTTP_USER_AGENT')) {
            return self::OTHER;
        }
        $userAgent = $server->get('HTTP_USER_AGENT');

        if (
            strpos($userAgent, "MSIE") !== false
            || strpos($userAgent, "Trident") !== false
            || strpos($userAgent, "Edge") !== false
        ) {
            return self::EDGE_IE_TRIDENT;
        }

        if (strpos($userAgent, "Firefox") !== false) {
            return self::FIREFOX;
        }

        if (strpos($userAgent, "Opera") !== false) {
            return self::OPERA;
        }

        if (strpos($userAgent, "Chrome") !== false) {
            return self::CHROME;
        }

        if (strpos($userAgent, "Safari") !== false) {
            return self::SAFARI;
        }

        return self::OTHER;
    }

}