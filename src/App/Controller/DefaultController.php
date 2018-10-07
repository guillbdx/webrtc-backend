<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function home()
    {
        return $this->render('front/default/home.html.twig');
    }

    /**
     * @Route("/test", name="test")
     * @return Response
     */
    public function test()
    {
        return new Response('<html><head><title>Test</title></head><body>test</body></html>');
    }

}
