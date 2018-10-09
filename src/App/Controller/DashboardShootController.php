<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ShootingStateService;
use App\Service\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/dashboard/shoot")
 */
class DashboardShootController extends AbstractController
{
    /**
     * @Route("/junction", name="dashboard_shoot_junction")
     * @param UserInterface|User $user
     * @param ShootingStateService $shootingStateService
     * @return Response
     */
    public function shootJunction(
        UserInterface $user,
        ShootingStateService $shootingStateService
    )
    {
        $shootingState = $shootingStateService->getUserShootingState($user);
        if (ShootingStateService::ACTIVE === $shootingState) {
            return $this->redirectToRoute('dashboard_shooter_active_state');
        }
        if (ShootingStateService::UNKNOWN === $shootingState) {
            return $this->redirectToRoute('dashboard_shooter_unknown_state');
        }

        return $this->redirectToRoute('dashboard_shoot_preparation');
    }

    /**
     * @Route("/shooter-active-state", name="dashboard_shooter_active_state")
     * @param UserInterface|User $user
     * @return Response
     */
    public function shooterInActiveState(
        UserInterface $user
    )
    {
        return $this->render('frontend/dashboard/shoot/shooter_in_active_state.html.twig');
    }

    /**
     * @Route("/shooter-unknown-state", name="dashboard_shooter_unknown_state")
     * @param UserInterface|User $user
     * @return Response
     */
    public function shooterInUnknownState(
        UserInterface $user
    )
    {
        return $this->render('frontend/dashboard/shoot/shooter_in_unknown_state.html.twig');
    }

    /**
     * @Route("/preparation", name="dashboard_shoot_preparation")
     * @return Response
     */
    public function shootPreparation()
    {
        return $this->render('frontend/dashboard/shoot/preparation.html.twig');
    }

    /**
     * @Route("/shoot/{allowedIceType}", name="dashboard_shoot_shoot")
     * @param UserInterface|User $user
     * @param SubscriptionService $subscriptionService
     * @param string $allowedIceType
     * @return Response
     */
    public function shoot(
        UserInterface $user,
        SubscriptionService $subscriptionService,
        string $allowedIceType = null
    )
    {
        if (false === $subscriptionService->canUseTheApplication($user)) {
            return $this->redirectToRoute('dashboard_shoot_not_allowed');
        }

        return $this->render('frontend/dashboard/shoot/shoot.html.twig', [
            'allowedIceType' => $allowedIceType
        ]);
    }

    /**
     * @Route("/camera-refused", name="dashboard_camera_refused")
     * @param UserInterface|User $user
     * @return Response
     */
    public function cameraRefuse(
        UserInterface $user
    )
    {
        return $this->render('frontend/dashboard/shoot/camera_refused.html.twig');
    }

    /**
     * @Route("/shoot-not-allowed", name="dashboard_shoot_not_allowed")
     * @param UserInterface|User $user
     * @return Response
     */
    public function shootNotAllowed(
        UserInterface $user
    )
    {
        return $this->render('frontend/dashboard/shoot/shoot_not_allowed.html.twig');
    }

}
