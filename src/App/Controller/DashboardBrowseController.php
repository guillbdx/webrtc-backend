<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\PhotoManager;
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
     * @Route("/", name="dashboard_browse")
     * @param UserInterface|User $user
     * @return Response
     */
    public function browse(
        UserInterface $user
    )
    {
        return $this->render('frontend/dashboard/browse/browse.html.twig');
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

    /**
     * @Route("/delete-all-preparation", name="dashboard_browse_delete_all_preparation")
     * @param UserInterface $user
     * @return Response
     */
    public function deleteAllPreparation(
        UserInterface $user
    )
    {
        return $this->render('frontend/dashboard/browse/delete_all_preparation.html.twig');
    }

    /**
     * @Route("/delete-all", name="dashboard_browse_delete_all")
     * @param PhotoManager $photoManager
     * @param UserInterface $user
     * @return Response
     */
    public function deleteAll(
        PhotoManager $photoManager,
        UserInterface $user
    )
    {
        $photoManager->removeUserPhotos($user);
        $this->addFlash('success', "Vos photos ont bien été supprimées.");
        return $this->redirectToRoute('dashboard_browse');
    }

}
