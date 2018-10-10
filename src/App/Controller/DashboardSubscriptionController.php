<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Factory\TransactionFactory;
use App\Form\Type\Dashboard\Subscription\QuantityType;
use App\Manager\TransactionManager;
use App\Model\Subscription;
use App\Service\StripeService;
use App\Service\SubscriptionService;
use Components\Emailing\AppMailer;
use Stripe\ApiResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/dashboard/subscription")
 */
class DashboardSubscriptionController extends AbstractController
{

    /**
     * @Route("/quantity/{quantity}", name="dashboard_subscription_quantity")
     * @param UserInterface|User $user
     * @param Request $request
     * @param int $quantity
     * @return Response
     */
    public function quantity(
        UserInterface $user,
        Request $request,
        int $quantity = 1
    )
    {
        $form = $this->createForm(QuantityType::class, ['quantity' => $quantity]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $quantity = $data['quantity'];
            return $this->redirectToRoute('dashboard_subscription_summary', [
                'quantity' => $quantity
            ]);
        }

        return $this->render('frontend/dashboard/subscription/quantity.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/summary/{quantity}", name="dashboard_subscription_summary")
     * @param UserInterface|User $user
     * @param int $quantity
     * @return Response
     */
    public function summary(
        UserInterface $user,
        int $quantity
    )
    {
        $subscription = new Subscription($quantity, $user);
        return $this->render('frontend/dashboard/subscription/summary.html.twig', [
            'subscription' => $subscription
        ]);
    }

    /**
     * @Route("/payment/{quantity}", name="dashboard_subscription_payment")
     * @param UserInterface|User $user
     * @param string $stripeApiKey
     * @param int $quantity
     * @return Response
     */
    public function payment(
        UserInterface $user,
        string $stripeApiKey,
        int $quantity
    )
    {
        $subscription = new Subscription($quantity, $user);
        return $this->render('frontend/dashboard/subscription/payment.html.twig', [
            'stripeApiKey' => $stripeApiKey,
            'subscription' => $subscription
        ]);
    }

    /**
     * @Route("/checkout", name="dashboard_subscription_checkout")
     *
     * @param UserInterface|User $user
     * @param Request $request
     * @param StripeService $stripeService
     * @param SubscriptionService $subscriptionService
     * @param TransactionFactory $transactionFactory
     * @param TransactionManager $transactionManager
     *
     * @return Response
     */
    public function checkout(
        UserInterface $user,
        Request $request,
        StripeService $stripeService,
        SubscriptionService $subscriptionService,
        TransactionFactory $transactionFactory,
        TransactionManager $transactionManager
    )
    {
        $tokenId = $request->request->get('tokenId');
        $quantity = $request->request->get('quantity');
        $subscription = new Subscription($quantity, $user);

        try {
            /** @var ApiResource $charge */
            $charge = $stripeService->charge($subscription, $tokenId);
        } catch (Exception $exception) {
            return $this->redirectToRoute('dashboard_subscription_failure');
        }

        $transaction = $transactionFactory->create($subscription, $charge->id);
        $transactionManager->save($transaction);

        $subscriptionService->applySubscription($subscription);

        return $this->redirectToRoute('dashboard_subscription_success', [
            'transaction' => $transaction->getId()
        ]);
    }

    /**
     * @Route("/success/{transaction}", name="dashboard_subscription_success")
     * @param UserInterface|User $user
     * @param Transaction $transaction
     * @param AppMailer $appMailer
     * @return Response
     */
    public function paymentSuccess(
        UserInterface $user,
        Transaction $transaction,
        AppMailer $appMailer
    )
    {
        if ($transaction->getUser()->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException();
        }
        $appMailer->sendTransactionConfirm($transaction);
        return $this->render('frontend/dashboard/subscription/success.html.twig', [
            'transaction' => $transaction
        ]);
    }

    /**
     * @Route("/failure", name="dashboard_subscription_failure")
     * @param UserInterface|User $user
     * @return Response
     */
    public function paymentFailure(
        UserInterface $user
    )
    {
        return $this->render('frontend/dashboard/subscription/failure.html.twig');
    }

}