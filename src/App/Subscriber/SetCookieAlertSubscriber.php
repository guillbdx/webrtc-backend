<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 10/11/18
 * Time: 8:02 PM
 */

namespace App\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SetCookieAlertSubscriber implements EventSubscriberInterface
{

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'setCookie'
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function setCookie(FilterResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (false === $event->isMasterRequest()) {
            return;
        }
        if ($request->headers->has('x-requested-with') && 'XMLHttpRequest' === $request->headers->get('x-requested-with')) {
            return;
        }

        if (!$request->cookies->has('cookieAlert')) {
            $cookie = new Cookie('cookieAlert', 'cookieAlert', time() + 3600 * 24 * 365);
            $response->headers->setCookie($cookie);
        }
    }

}