<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace Components\Emailing;

use App\Entity\Photo;
use App\Entity\Transaction;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;

class AppMailer
{

    public const SEND_EMAIL_CHECK_TOKEN         = 'SEND_EMAIL_CHECK_TOKEN';
    public const SEND_PASSWORD_REQUEST_TOKEN    = 'SEND_PASSWORD_REQUEST_TOKEN';
    public const ALARM                          = 'ALARM';
    public const CONTACT_MESSAGE                = 'CONTACT_MESSAGE';
    public const WITHDRAWAL                     = 'WITHDRAWAL';
    public const TRANSACTION_CONFIRMATION       = 'TRANSACTION_CONFIRMATION';

    /**
     * @var \Swift_Mailer
     */
    private $swiftMailer;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $host;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param \Swift_Mailer $swift_Mailer
     * @param \Twig_Environment $twig
     * @param string $host
     * @param RouterInterface $router
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Swift_Mailer $swift_Mailer,
        \Twig_Environment $twig,
        string $host,
        RouterInterface $router,
        LoggerInterface $logger
    )
    {
        $this->swiftMailer = $swift_Mailer;
        $this->twig = $twig;
        $this->host = $host;
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendEmailCheckToken(User $user): void
    {
        $message = (new \Swift_Message('Merci de confirmer votre email'))
            ->setFrom(['no-reply@'.$this->host => 'Dilcam'])
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('email/email_check_token.html.twig', [
                    'user' => $user
                ]),
                'text/html'
            )
        ;

        $this->logger->info(self::SEND_EMAIL_CHECK_TOKEN, [
            'to' => $user->getEmail()
        ]);
        $this->swiftMailer->send($message);
    }

    /**
     * @param User $user
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendPasswordResetToken(User $user): void
    {
        $message = (new \Swift_Message('Réinitialisation de votre mot de passe'))
            ->setFrom(['no-reply@'.$this->host => 'Dilcam'])
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('email/password_reset_token.html.twig', [
                    'user' => $user
                ]),
                'text/html'
            )
        ;

        $this->logger->info(self::SEND_PASSWORD_REQUEST_TOKEN, [
            'to' => $user->getEmail()
        ]);
        $this->swiftMailer->send($message);
    }

    /**
     * @param User $user
     * @param Photo $photo
     */
    public function sendAlarm(User $user, Photo $photo): void
    {
        $message = (new \Swift_Message('Mouvement détecté'))
            ->setFrom(['no-reply@'.$this->host => 'Dilcam'])
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('email/alarm.html.twig', [
                    'user' => $user,
                    'photo' => $photo
                ]),
                'text/html'
            )
        ;

        $this->logger->info(self::ALARM, [
            'to' => $user->getEmail(),
            'photo' => $photo->getId()
        ]);
        $this->swiftMailer->send($message);
    }

    /**
     * @param string $from
     * @param string $message
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendContactMessage(string $from, string $message)
    {
        $message = (new \Swift_Message('Message'))
            ->setFrom(['no-reply@'.$this->host => 'Dilcam'])
            ->setTo('contact@'.$this->host)
            ->setBody(
                $this->twig->render('email/contact_message.html.twig', [
                    'from' => $from,
                    'message' => $message
                ]),
                'text/html'
            )
        ;

        $this->logger->info(self::CONTACT_MESSAGE, [
            'from' => $from,
            'to' => 'contact@'.$this->host
        ]);

        $this->swiftMailer->send($message);
    }

    /**
     * @param string $email
     * @param string $transactionReference
     * @param string $reason
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendWithdrawal(string $email, string $transactionReference, ?string $reason = null): void
    {
        $message = (new \Swift_Message('Rétractation'))
            ->setFrom(['no-reply@'.$this->host => 'Dilcam'])
            ->setTo('contact@'.$this->host)
            ->setBody(
                $this->twig->render('email/withdrawal.html.twig', [
                    'email' => $email,
                    'transactionReference' => $transactionReference,
                    'reason' => $reason
                ]),
                'text/html'
            )
        ;

        $this->logger->info(self::WITHDRAWAL, [
            'email' => $email,
            'transactionReference' => $transactionReference,
            'to' => 'contact@'.$this->host
        ]);

        $this->swiftMailer->send($message);
    }

    /**
     * @param Transaction $transaction
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendTransactionConfirm(Transaction $transaction): void
    {
        $user = $transaction->getUser();
        $message = (new \Swift_Message('Votre commande a été validée'))
            ->setFrom(['no-reply@'.$this->host => 'Dilcam'])
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('email/transaction_confirmation.html.twig', [
                    'transaction' => $transaction
                ]),
                'text/html'
            )
        ;

        $this->logger->info(self::TRANSACTION_CONFIRMATION, [
            'transaction' => $transaction->getId(),
            'to' => $user->getEmail()
        ]);

        $this->swiftMailer->send($message);
    }

}