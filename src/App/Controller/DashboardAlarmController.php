<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/dashboard/alarm")
 */
class DashboardAlarmController extends AbstractController
{
    /**
     * @Route("/", name="dashboard_alarm")
     * @param UserInterface|User $user
     * @return Response
     */
    public function alarm(
        UserInterface $user
    )
    {
        return $this->render('front/dashboard/alarm/alarm.html.twig');
    }

    /**
     * @Route("/enable", name="dashboard_alarm_enable")
     * @param UserInterface|User $user
     * @param UserManager $userManager
     * @return Response
     */
    public function enableAlarm(
        UserInterface $user,
        UserManager $userManager
    )
    {
        $userManager->enableAlarm($user);
        $this->addFlash('success', "L'alarme a été activée.");

        return $this->redirectToRoute('dashboard_alarm');
    }

    /**
     * @Route("/disable", name="dashboard_alarm_disable")
     * @param UserInterface|User $user
     * @param UserManager $userManager
     * @return Response
     */
    public function disableAlarm(
        UserInterface $user,
        UserManager $userManager
    )
    {
        $userManager->disableAlarm($user);
        $this->addFlash('success', "L'alarme a été désactivée.");

        return $this->redirectToRoute('dashboard_alarm');
    }

}
