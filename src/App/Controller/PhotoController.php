<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Controller; 

use App\Entity\Photo;
use App\Form\Type\Photo\MismatchType;
use App\Form\Type\Photo\PhotoType;
use App\Manager\PhotoManager;
use App\Manager\UserManager;
use App\Security\PhotoVoter;
use App\Service\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/photo")
 */
class PhotoController extends AbstractController
{

    /**
     * @Route("/snap", name="photo_snap")
     *
     * @param Request $request
     * @param PhotoManager $photoManager
     * @param SubscriptionService $subscriptionService
     *
     * @return Response
     */
    public function snap(
        Request $request,
        PhotoManager $photoManager,
        SubscriptionService $subscriptionService
    )
    {
        $form = $this->createForm(PhotoType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())  {

            /** @var Photo $photo */
            $photo = $form->getData();
            if (false === $subscriptionService->canUseTheApplication($photo->getUser())) {
                throw new AccessDeniedHttpException();
            }

            $subscriptionService->incrementUseDuration($photo->getUser(), 60);
            $photoManager->save($photo);
            return new Response('');
        }

        return $this->render('frontend/default/photo/snap.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mismatch", name="photo_mismatch")
     *
     * @param Request $request
     * @param PhotoManager $photoManager
     * @param UserManager $userManager
     * @param SubscriptionService $subscriptionService
     *
     * @return Response
     */
    public function mismatch(
        Request $request,
        PhotoManager $photoManager,
        UserManager $userManager,
        SubscriptionService $subscriptionService
    )
    {
        $form = $this->createForm(MismatchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())  {
            $data = $form->getData();

            /** @var Photo $photoAfter */
            $photoAfter = $data['photoAfter'];
            $photoBefore = $data['photoBefore'];
            $mismatch = $data['mismatch'];

            if (false === $subscriptionService->canUseTheApplication($photoAfter->getUser())) {
                throw new AccessDeniedHttpException();
            }

            $photoManager->saveMismatch($photoBefore, $photoAfter, $mismatch);
            $userManager->sendAlarmIfNeed($photoAfter, $mismatch);
            return new Response('');
        }

        return $this->render('frontend/default/photo/mismatch.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/show/{photo}/{download}", name="photo_show")
     * @param Photo $photo
     * @param PhotoManager $photoManager
     * @param bool $download
     * @return Response
     */
    public function show(
        Photo $photo,
        PhotoManager $photoManager,
        $download = false
    )
    {
        $this->denyAccessUnlessGranted(PhotoVoter::SHOW, $photo);
        $blob = $photoManager->getPhotoBlob($photo);
        if (null == $blob) {
            $blob = base64_decode("/9j/4AAQSkZJRgABAQEASABIAAD//gATQ3JlYXRlZCB3aXRoIEdJTVD/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoHBwYIDAoMDAsKCwsNDhIQDQ4RDgsLEBYQERMUFRUVDA8XGBYUGBIUFRT/2wBDAQMEBAUEBQkFBQkUDQsNFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBT/wgARCAADAAQDAREAAhEBAxEB/8QAFAABAAAAAAAAAAAAAAAAAAAACP/EABQBAQAAAAAAAAAAAAAAAAAAAAD/2gAMAwEAAhADEAAAASof/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABBQJ//8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAwEBPwF//8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAgEBPwF//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQAGPwJ//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPyF//9oADAMBAAIAAwAAABCf/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAwEBPxB//8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAgEBPxB//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxB//9k=");
        }

        $response = new Response();

        $photoName = 'dilcam-'.$photo->getCreatedAt()->format('Y-m-d-H-i-s').'.jpg';
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $photoName);
        if ($download) {
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $photoName);
        }
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->setContent($blob);

        return $response;
    }

    /**
     * @Route("/show-secret/{photo}/{secret}", name="photo_show_secret")
     * @param Photo $photo
     * @param string $secret
     * @param PhotoManager $photoManager
     * @return Response
     */
    public function showPhotoWithSecret(
        Photo $photo,
        string $secret,
        PhotoManager $photoManager
    )
    {
        if ($secret !== $photo->getSecret()) {
            throw new NotFoundHttpException();
        }
        $blob = $photoManager->getPhotoBlob($photo);
        if (null == $blob) {
            $blob = base64_decode("/9j/4AAQSkZJRgABAQEASABIAAD//gATQ3JlYXRlZCB3aXRoIEdJTVD/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoHBwYIDAoMDAsKCwsNDhIQDQ4RDgsLEBYQERMUFRUVDA8XGBYUGBIUFRT/2wBDAQMEBAUEBQkFBQkUDQsNFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBT/wgARCAADAAQDAREAAhEBAxEB/8QAFAABAAAAAAAAAAAAAAAAAAAACP/EABQBAQAAAAAAAAAAAAAAAAAAAAD/2gAMAwEAAhADEAAAASof/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABBQJ//8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAwEBPwF//8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAgEBPwF//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQAGPwJ//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPyF//9oADAMBAAIAAwAAABCf/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAwEBPxB//8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAgEBPxB//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxB//9k=");
        }

        $response = new Response();

        $photoName = 'dilcam-'.$photo->getCreatedAt()->format('Y-m-d-H-i-s').'.jpg';
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $photoName);
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->setContent($blob);

        return $response;
    }

}
