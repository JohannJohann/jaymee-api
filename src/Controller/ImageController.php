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
use App\Service\FirebasePushService;

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
    public function new(Request $request, FirebasePushService $pushService): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        $user = $uRepository->findOneByToken($request->headers->get('Authorization'));
        $photo = $request->files->get('photo');
        $newName = $user->getId().'-'.((new \DateTime())->getTimestamp()).'.'.$photo->getClientOriginalExtension();

        $photo->move(getenv('PRIVATE_PHOTO_STORAGE').'/'.$user->getId(), $newName);

        $image = new Image();
        $image->setNumber($request->request->get('number'));
        $image->setFilename($newName);
        $image->setCost(5);
        $image->setCreatedAt( new \Datetime());
        $image->setOwner($user);

        $user->setLastActivityAt(new \DateTime());

        $em->persist($image);
        $em->flush();

        foreach($user->getFollowedBy() as $follower){
            if(!is_null($follower->getFcmToken())){
                $informations = array(
                    "source" => $user->getUsername(),
                    "target" => $follower->getFcmToken(),
                    "code" => 1,
                );
                $pushService->sendPushNotification($informations);
            }
        }
        return new JsonResponse($image->getId());
    }

    /**
     * @Route("/unlock", name="unlock_photo", methods={"POST"})
     */
    public function unlock(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        $iRepository = $em->getRepository(Image::class);
        $user = $uRepository->findOneByToken($request->headers->get('Authorization'));
        
        $photo = $iRepository->find($request->request->get('photoId'));
        if($photo){
            $canUnlock = $photo->getOwner()->countSharedKeysFor($user) >= $photo->getCost();
            if($canUnlock){
                $key  = $photo->getOwner()->getSharedKeysFor($user);
                $key->setQuantity($key->getQuantity() - $photo->getCost());
                $photo->addViewedBy($user);

                $imagePath = getenv('PRIVATE_PHOTO_STORAGE').'/'.$photo->getOwner()->getId().'/'.$photo->getFilename();
                $image = base64_encode(file_get_contents($imagePath));
                $em->flush();
                return new JsonResponse(array('image' => $image));    
            } else {
                return new JsonResponse();
            }
        } else {
            return new JsonResponse();
        }
    }
}
