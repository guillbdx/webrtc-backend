<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace Components\Emailing;

use App\Entity\Photo;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;

class AppMailer
{

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

        $this->logger->info('Send Email Check Token', ['to' => $user->getEmail()]);
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

        $this->logger->info('Send Password Reset Token', ['to' => $user->getEmail()]);
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

        $this->logger->info('Send Password Reset Token', ['to' => $user->getEmail()]);
        $this->swiftMailer->send($message);
    }

    public function sendContactMessage(string $from, string $message)
    {
        $message = (new \Swift_Message('Message'))
            ->setFrom(['no-reply@'.$this->host => 'Dilcam'])
            ->setTo('gpedelagrabe@gmail.com')
            ->setBody(
                $this->twig->render('email/contact_message.html.twig', [
                    'from' => $from,
                    'message' => $message
                ]),
                'text/html'
            )
        ;

        $this->logger->info('Contact message', [
            'from' => $from,
            'to' => 'gpedelagrabe@gmail.com'
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
            ->setTo('gpedelagrabe@gmail.com')
            ->setBody(
                $this->twig->render('email/withdrawal.html.twig', [
                    'email' => $email,
                    'transactionReference' => $transactionReference,
                    'reason' => $reason
                ]),
                'text/html'
            )
        ;

        $this->logger->info('Withdrawal', [
            'email' => $email,
            'transactionReference' => $transactionReference,
            'to' => 'gpedelagrabe@gmail.com'
        ]);

        $this->swiftMailer->send($message);
    }

}