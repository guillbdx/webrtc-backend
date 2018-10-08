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
        return $this->render('frontend/default/home.html.twig');
    }

    /**
     * @Route("/test", name="test")
     * @return Response
     */
    public function test()
    {
        return new Response('<html><head><title>Test</title></head><body>test</body></html>');
    }

    /**
     * @Route("/guide", name="guide")
     * @return Response
     */
    public function guide()
    {
        return $this->render('frontend/default/guide.html.twig');
    }

    /**
     * @Route("/tos", name="tos")
     * @return Response
     */
    public function tos()
    {
        return $this->render('frontend/default/tos.html.twig');
    }

    /**
     * @Route("/legal", name="legal")
     * @return Response
     */
    public function legal()
    {
        return $this->render('frontend/default/legal.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     * @return Response
     */
    public function contact()
    {
        return $this->render('frontend/default/contact.html.twig');
    }

}
