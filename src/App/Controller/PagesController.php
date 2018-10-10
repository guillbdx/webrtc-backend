<?php

namespace App\Controller;

use App\Form\Type\Pages\ContactType;
use App\Service\OperatingSystemDetector;
use Components\Emailing\AppMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     * @param AppMailer $appMailer
     * @return Response
     */
    public function contact(
        Request $request,
        AppMailer $appMailer
    )
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        $displayForm = true;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $appMailer->sendContactMessage($data['from'], $data['message']);
            $this->addFlash('success', "Merci, votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.");
            $displayForm = false;
        }

        return $this->render('frontend/default/pages/contact.html.twig', [
            'form' => $form->createView(),
            'displayForm' => $displayForm
        ]);
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

    /**
     * @Route("/cookies", name="pages_cookies")
     * @return Response
     */
    public function cookies()
    {
        return $this->render('frontend/default/pages/cookies.html.twig');
    }

    /**
     * @Route("/hardware", name="pages_hardware")
     * @return Response
     */
    public function hardware()
    {
        return $this->render('frontend/default/pages/hardware.html.twig');
    }

    /**
     * @Route("/alarm", name="pages_alarm")
     * @return Response
     */
    public function alarm()
    {
        return $this->render('frontend/default/pages/alarm.html.twig');
    }

    /**
     * @Route("/sleeping", name="pages_sleeping")
     * @param OperatingSystemDetector $operatingSystemDetector
     * @return Response
     */
    public function sleeping(
        OperatingSystemDetector $operatingSystemDetector
    )
    {
        return $this->render('frontend/default/pages/sleeping.html.twig', [
            'operatingSystem' => $operatingSystemDetector->detect()
        ]);
    }

}
