<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Subscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RedirectIfEmailNotCheckedSubscriber implements EventSubscriberInterface
{

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * RedirectIfEmailNotCheckedSubscriber constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param RouterInterface $router
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        RouterInterface $router
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'redirect'
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function redirect(GetResponseEvent $event): void
    {
        $request = $event->getRequest();
        $pathinfo = $request->getPathInfo();
        if ('dashboard_pending_email_check' === $request->get('_route')) {
            return;
        }
        if ('/dashboard' !== substr($pathinfo, 0, 10)) {
            return;
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        if (false === $user->isEmailChecked()) {
            $response = new RedirectResponse($this->router->generate('dashboard_pending_email_check'));
            $event->setResponse($response);
        }
    }

}