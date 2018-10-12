<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 10/12/18
 * Time: 4:52 PM
 */

namespace App\Subscriber;

use App\Service\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ShootingTokenCookieSubscriber implements EventSubscriberInterface
{

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @param TokenGenerator $tokenGenerator
     */
    public function __construct(
        TokenGenerator $tokenGenerator
    )
    {
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'setTokenCookie'
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function setTokenCookie(FilterResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (false === $event->isMasterRequest()) {
            return;
        }

        if ($request->headers->has('x-requested-with') && 'XMLHttpRequest' === $request->headers->get('x-requested-with')) {
            return;
        }

        $token = $this->tokenGenerator->generate();

        if (!$request->cookies->has('shootingToken')) {
            $cookie = new Cookie('shootingToken', $token, time() + 3600 * 24 * 365);
            $response->headers->setCookie($cookie);
        }
    }

}