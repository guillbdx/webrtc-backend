<?php

namespace Components\Captcha\Service;

use Symfony\Component\HttpFoundation\Request;

class CaptchaService
{

    /**
     * @var string
     */
    private $reCaptchaApiKeySecret;

    /**
     * @param string $reCaptchaApiKeySecret
     */
    public function __construct(
        string $reCaptchaApiKeySecret
    )
    {
        $this->reCaptchaApiKeySecret = $reCaptchaApiKeySecret;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isRequestValid(Request $request): bool
    {
        if (false === $request->request->has('g-recaptcha-response')) {
            return false;
        }

        $token = $request->request->get('g-recaptcha-response');
        $reCaptchaClient = new ReCaptchaClient($this->reCaptchaApiKeySecret);
        $reCaptchaResponse = $reCaptchaClient->verifyResponse(
            $request->server->get('REMOTE_ADDR'),
            $token
        );

        return $reCaptchaResponse->success;
    }

}