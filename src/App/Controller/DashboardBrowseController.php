<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/dashboard/browse")
 */
class DashboardBrowseController extends AbstractController
{

    /**
     * @Route("/", name="dasboard_browse")
     * @param UserInterface|User $user
     * @return Response
     */
    public function browse(
        UserInterface $user
    )
    {
        return $this->render('front/dashboard/browse/browse.html.twig');
    }

    /**
     * @Route("/all-photos", name="dashboard_browse_all_photos")
     * @param UserInterface|User $user
     * @param PhotoRepository $photoRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function allPhotos(
        UserInterface $user,
        PhotoRepository $photoRepository,
        SerializerInterface $serializer
    )
    {
        $photos = $photoRepository->findRegularAndAfterPhotosByUser($user);
        $photos = $serializer->serialize($photos, 'json', ['groups' => ['browse']]);

        return JsonResponse::fromJsonString($photos);
    }

}
