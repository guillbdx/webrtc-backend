<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\SoftwareDetector;
use App\Service\ShootingStateService;
use App\Service\SubscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @param SubscriptionService $subscriptionService
     * @param ShootingStateService $shootingStateService
     * @return Response
     */
    public function shootJunction(
        UserInterface $user,
        SubscriptionService $subscriptionService,
        ShootingStateService $shootingStateService
    )
    {
        if (false === $subscriptionService->canUseTheApplication($user)) {
            return $this->redirectToRoute('dashboard_subscription_manage');
        }

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
     * @param SoftwareDetector $softwareDetector
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param string $allowedIceType
     * @return Response
     */
    public function shoot(
        UserInterface $user,
        SubscriptionService $subscriptionService,
        SoftwareDetector $softwareDetector,
        Request $request,
        EntityManagerInterface $entityManager,
        string $allowedIceType = null
    )
    {
        if (false === $subscriptionService->canUseTheApplication($user)) {
            return $this->redirectToRoute('dashboard_subscription_manage');
        }

        $user->setShootingToken($request->cookies->get('shootingToken'));
        $entityManager->flush();

        return $this->render('frontend/dashboard/shoot/shoot.html.twig', [
            'allowedIceType' => $allowedIceType,
            'operatingSystem' => $softwareDetector->detectOperatingSystem()
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

}
