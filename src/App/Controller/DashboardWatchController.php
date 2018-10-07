<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/dashboard/watch")
 */
class DashboardWatchController extends AbstractController
{

    /**
     * @Route("/watch/{allowedIceType}", name="dashboard_watch_watch")
     * @param UserInterface|User $user
     * @param SubscriptionService $subscriptionService
     * @param string $allowedIceType
     * @return Response
     */
    public function watch(
        UserInterface $user,
        SubscriptionService $subscriptionService,
        string $allowedIceType = null
    )
    {
        /** @var User $user */
        $user = $this->getUser();
        if (false === $subscriptionService->canUseTheApplication($user)) {
            return $this->redirectToRoute('dashboard_watch_not_allowed');
        }

        return $this->render('front/dashboard/watch/watch.html.twig', [
            'allowedIceType' => $allowedIceType
        ]);
    }

    /**
     * @Route("/not-allowed", name="dashboard_watch_not_allowed")
     * @param UserInterface|User $user
     * @return Response
     */
    public function watchNotAllowed(
        UserInterface $user
    )
    {
        return $this->render('front/dashboard/watch/watch_not_allowed.html.twig');
    }

    /**
     * @Route("/timeout", name="dashboard_watch_timeout")
     * @param UserInterface|User $user
     * @return Response
     */
    public function watchTimeout(
        UserInterface $user
    )
    {
        return $this->render('front/dashboard/watch/watch_timeout.html.twig');
    }

}
