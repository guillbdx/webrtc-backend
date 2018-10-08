<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pages")
 */
class PagesController extends AbstractController
{

    /**
     * @Route("/guide", name="pages_guide")
     * @return Response
     */
    public function guide()
    {
        return $this->render('frontend/default/pages/guide.html.twig');
    }

    /**
     * @Route("/tos", name="pages_tos")
     * @return Response
     */
    public function tos()
    {
        return $this->render('frontend/default/pages/tos.html.twig');
    }

    /**
     * @Route("/legal", name="pages_legal")
     * @return Response
     */
    public function legal()
    {
        return $this->render('frontend/default/pages/legal.html.twig');
    }

    /**
     * @Route("/contact", name="pages_contact")
     * @return Response
     */
    public function contact()
    {
        return $this->render('frontend/default/pages/contact.html.twig');
    }

    /**
     * @Route("/pricing", name="pages_pricing")
     * @return Response
     */
    public function pricing()
    {
        return $this->render('frontend/default/pages/pricing.html.twig');
    }

    /**
     * @Route("/confidentiality", name="pages_confidentiality")
     * @return Response
     */
    public function confidentiality()
    {
        return $this->render('frontend/default/pages/confidentiality.html.twig');
    }

}
