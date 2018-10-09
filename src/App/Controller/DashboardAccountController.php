<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\Dashboard\ChangePasswordType;
use App\Manager\UserManager;
use App\Repository\TransactionRepository;
use App\Service\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/dashboard/account")
 */
class DashboardAccountController extends AbstractController
{
    /**
     * @Route("/", name="dashboard_account")
     * @param SubscriptionService $subscriptionService
     * @param UserInterface|User $user
     * @return Response
     */
    public function account(
        SubscriptionService $subscriptionService,
        UserInterface $user
    )
    {
        return $this->render('frontend/dashboard/account/account.html.twig', [
            'userStatus' => $subscriptionService->getUserStatus($user)
        ]);
    }

    /**
     * @Route("/change-password", name="dashboard_account_change_password")
     * @param UserInterface|User $user
     * @param Request $request
     * @param UserManager $userManager
     * @return Response
     */
    public function changePassword(
        UserInterface $user,
        Request $request,
        UserManager $userManager
    )
    {
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->changePassword($user);
            $this->addFlash('success', 'Votre mot de passe a été mis à jour.');
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('frontend/dashboard/account/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/transactions", name="dashboard_account_transactions")
     * @param UserInterface|User $user
     * @param TransactionRepository $transactionRepository
     * @return Response
     */
    public function transactionsList(
        UserInterface $user,
        TransactionRepository $transactionRepository
    )
    {
        $transactions = $transactionRepository->findByUser($user);

        return $this->render('frontend/dashboard/account/transactions.html.twig', [
            'transactions' => $transactions
        ]);
    }

}
