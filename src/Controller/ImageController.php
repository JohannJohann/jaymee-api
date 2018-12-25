<?php

namespace App\Controller;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization");

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Image;
use App\Entity\User;

/**
 * @Route("/photo")
 */
class ImageController extends AbstractController
{   
    // const PUBLIC_PHOTO_STORAGE = __DIR__."/public/profiles";
    const PRIVATE_PHOTO_STORAGE = "/storage/";

    /**
     * @Route("/all", name="get_all_for_user")
     */
    public function getAll(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        $pRepository = $em->getRepository(Image::class);
        $user = $uRepository->findOneByToken($request->headers->get('Authorization'));

        $photos = $pRepository->getAll($user);

        return new JsonResponse($photos);
    }

    /**
     * @Route("/new", name="add_new_photo", methods={"POST"})
     */
    public function new(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        $user = $uRepository->findOneByToken($request->headers->get('Authorization'));
        $photo = $request->files->get('photo');
        $newName = $user->getId().'-'.((new \DateTime())->getTimestamp()).'.'.$photo->getClientOriginalExtension();

        $photo->move(self::PRIVATE_PHOTO_STORAGE.'/'.$user->getId(), $newName);

        $image = new Image();
        $image->setNumber($request->request->get('number'));
        $image->setFilename($newName);
        $image->setCost(5);
        $image->setCreatedAt( new \Datetime());
        $image->setOwner($user);

        $em->persist($image);
        $em->flush();

        return new JsonResponse($image->getId());
    }
}
