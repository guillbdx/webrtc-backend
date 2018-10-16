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
        if (false === $subscriptionService->canUseTheApplication($user)) {
            return $this->redirectToRoute('dashboard_subscription_manage');
        }

        return $this->render('frontend/dashboard/watch/watch.html.twig', [
            'allowedIceType' => $allowedIceType
        ]);
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
        return $this->render('frontend/dashboard/watch/watch_timeout.html.twig');
    }

}
