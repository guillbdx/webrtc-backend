<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use App\Service\ShootingStateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/dashboard")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     * @param ShootingStateService $shootingStateService
     * @param UserInterface|User $user
     * @return Response
     */
    public function dashboard(
        ShootingStateService $shootingStateService,
        UserInterface $user
    )
    {
        $shootingState = $shootingStateService->getUserShootingState($user);

        return $this->render('frontend/dashboard/dashboard.html.twig', [
            'currentlyShooting' => ShootingStateService::ACTIVE === $shootingState
        ]);
    }

    /**
     * @Route("/pending-email-check/{send}", name="dashboard_pending_email_check")
     * @param UserInterface|User $user
     * @param UserManager $userManager
     * @param bool $send
     * @return Response
     */
    public function pendingEmailCheck(
        UserInterface $user,
        UserManager $userManager,
        bool $send = false
    )
    {
        if (true === $user->isEmailChecked()) {
            return $this->redirectToRoute('dashboard');
        }
        if (true === $send) {
            $userManager->resendEmailCheckToken($user);
            $this->addFlash('success', "L'email de confirmation vient de vous être renvoyé.");
        }
        return $this->render('frontend/dashboard/pending_email_check.html.twig', [
            'send' => $send
        ]);
    }

}
