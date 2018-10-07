<?php

namespace App\Controller;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Form\Type\Security\LoginType;
use App\Form\Type\Security\ResetPasswordRequestType;
use App\Form\Type\Security\ResetPasswordType;
use App\Form\Type\Security\SignupType;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/security")
 */
class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="security_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(
        AuthenticationUtils $authenticationUtils
    )
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $form = $this->createForm(LoginType::class, null, [
            'lastUsername' => $authenticationUtils->getLastUsername()
        ]);

        return $this->render('frontend/default/security/login.html.twig', array(
            'error'         => $error,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/signup", name="security_signup")
     * @param Request $request
     * @param UserManager $userManager
     * @param UserFactory $userFactory
     * @return Response
     */
    public function signup(
        Request $request,
        UserManager $userManager,
        UserFactory $userFactory
    )
    {
        $user = $userFactory->init();
        $form = $this->createForm(SignupType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->signup($user);
            return $this->redirectToRoute('security_signup_confirmation');
        }

        return $this->render('frontend/default/security/signup.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/signup-confirmation", name="security_signup_confirmation")
     * @return Response
     */
    public function signupConfirm()
    {
        return $this->render('frontend/default/security/signup_confirmation.html.twig');
    }

    /**
     * @Route("/check-email/{emailCheckToken}", name="security_check_email")
     * @param string $emailCheckToken
     * @param UserRepository $userRepository
     * @param UserManager $userManager
     * @return Response
     */
    public function checkEmail(
        string $emailCheckToken,
        UserRepository $userRepository,
        UserManager $userManager
    )
    {
        $fetchedUser = $userRepository->findOneBy(['emailCheckToken' => $emailCheckToken]);
        if (false === $fetchedUser instanceof User) {
            return $this->render('frontend/default/security/wrong_check_email_token.html.twig');
        }
        $userManager->validateEmail($fetchedUser);

        $user = $this->getUser();
        if (false === $user instanceof User) {
            $userManager->log($fetchedUser);
        }

        $this->addFlash('success', 'Votre email a bien été confirmé.');

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/reset-password-request", name="security_reset_password_request")
     * @param Request $request
     * @param UserManager $userManager
     * @return Response
     */
    public function resetPasswordRequest(
        Request $request,
        UserManager $userManager
    )
    {
        $form = $this->createForm(ResetPasswordRequestType::class);
        $form->handleRequest($request);
        $display = true;
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData()['user'];
            $userManager->requestPasswordReset($user);
            $this->addFlash('success', 'Un email avec un lien vous a été envoyé.');
            $display = false;
        }
        return $this->render('frontend/default/security/reset_password_request.html.twig', [
            'form' => $form->createView(),
            'display' => $display
        ]);
    }

    /**
     * @Route("/reset-password/{passwordResetToken}", name="security_reset_password")
     * @param UserManager $userManager
     * @param Request $request
     * @param string $passwordResetToken
     * @param UserRepository $userRepository
     * @return Response
     */
    public function resetPassword(
        UserManager $userManager,
        Request $request,
        string $passwordResetToken,
        UserRepository $userRepository
    )
    {
        $fetchedUser = $userRepository->findOneBy(['passwordResetToken' => $passwordResetToken]);
        if (false === $fetchedUser instanceof User) {
            return $this->render('frontend/default/security/wrong_password_reset_token.html.twig');
        }

        $form = $this->createForm(ResetPasswordType::class, $fetchedUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->changePassword($fetchedUser);
            $userManager->log($fetchedUser);
            $this->addFlash('success', 'Votre mot de passe a été mis à jour.');
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('frontend/default/security/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
