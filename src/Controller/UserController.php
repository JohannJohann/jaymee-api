<?php

namespace App\Controller;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization");

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken,
    Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
    
use App\Controller\ImageController;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Quizz;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $passwordEncoder;
    private $dispatcher;
    private $defaultEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EventDispatcherInterface $dispatcher)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->dispatcher = $dispatcher;
        $this->defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
    }
    
    /**
     * @Route("/signin", name="sign_in", methods={"POST"})
     */
    public function signIn(Request $request){
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        $response = new JsonResponse();

        $user = $uRepository->findOneBy(array('username'=>$request->request->get("username")));
        if($user==null){

            // Création de l'utilisateur
            $name =  $request->request->get("username");
            $newUser = new User();
            $newUser->setUsername($name);
            $newUser->setSalt('');
            $newUser->setPassword( $this->defaultEncoder->encodePassword($request->request->get("password"), ''));
            $newUser->setRoles(["ROLE_USER"]);
            $newUser->setFcmToken($request->request->get("fcm_token"));
            $newUser->setLastActivityAt(new \DateTime());
            $newUser->setHasPrivileges(false);

            $em->persist($newUser);

            // Fin de la création
            $em->flush();
            $user= $newUser;

            // Login manuel
            $token = $this->randomString(100);
            $user->setToken($token);
            $em->flush();

            $userWithToken = [
                'username' => $user->getUsername(),
                'profile_pic' => $user->getProfilePic(),
                'last_activity_at' => $user->getLastActivityAt(),
                'has_privileges' => $user->getHasPrivileges(),
                'token' => $token
            ];
            $response->setData($userWithToken);
            return $response;
        }
        else {
            $response->setData(array('success'=>false,'error'=>'Ce nom d\'utilisateur est déjà enregistré sur Jaymee')); 
            return $response;
        }
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request){
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        $response = new JsonResponse();

        $user = $uRepository->findOneBy(array('username'=>$request->request->get("_username")));
        if($user == null){
            return $response;
        }

        $attempt = $this->defaultEncoder->encodePassword($request->request->get("_password"), $user->getSalt());
        if($attempt == $user->getPassword()){
            $user->setFcmToken($request->request->get("fcm_token"));
            $token = $this->randomString(100);
            $user->setToken($token);
            $em->flush();
            $userWithToken = [
                'username' => $user->getUsername(),
                'has_privileges' => $user->getHasPrivileges(),
                'profile_pic' => $user->getProfilePic(),
                'token' => $token,
                'hasFeed' => !$user->getFollowing()->isEmpty(),
            ];
            $response->setData($userWithToken);
        } 
        return $response;
    }

    /**
     * @Route("/logged", name="get_authenticated_user", methods={"GET"})
     */
    public function getLoggedUser(Request $request){
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);

        $token = $request->headers->get('Authorization');
        $user = $uRepository->findOneByToken($token);

        if($user !== null)
        {
            $user->hasFeed = !$user->getFollowing()->isEmpty();
            return new JsonResponse($user);
        }
        else {
            return new JsonResponse();
        }
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"},name="get_user", methods={"GET"})
     */
    public function getUserData(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        $qRepository = $em->getRepository(Quizz::class);

        $token = $request->headers->get('Authorization');
        $loggedUser = $uRepository->findOneByToken($token);

        $user = $uRepository->find($id);

        if($user !== null)
        {
            $user->isFollowed = $loggedUser->getFollowing()->contains($user);
            $user->keysForMe = $user->countSharedKeysFor($loggedUser);
            $user->hasQuizzForMe = !empty($qRepository->getRandom($loggedUser, $user));
            $user->photos = $user->getOwnImages()->filter(function(Image $image) use ($loggedUser) {
                return !$image->getViewedBy()->contains($loggedUser);
            })
            ->map(function(Image $image){
                $image->viewedBy = $image->getViewedBy()->toArray();
                return $image;
            })
            ->getValues();
            return new JsonResponse($user);
        }
        else {
            return new JsonResponse();
        }
    }

    /**
     * @Route("/logout", name="logout_user", methods={"GET"})
     */
    public function logoutUser(Request $request){
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);

        $token = $request->headers->get('Authorization');
        $user = $uRepository->findOneByToken($token);
        if($user !=null){
            $user->setToken(null);
            $em->flush();
        }
        return new Response();
    }

      /**
     * @Route("/profilepic", name="add_profile_pic", methods={"POST"})
     */
    public function addProfilePic(Request $request) : Response {
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);

        $token = $request->headers->get('Authorization');
        $user = $uRepository->findOneByToken($token);
        if($user !=null){
            $photo = $request->files->get('photo');
            $photoFolder = getenv('PUBLIC_PHOTO_STORAGE');
            $newName = $user->getId().'-'.((new \DateTime())->getTimestamp()).'.'.$photo->getClientOriginalExtension();

            $photo->move(__DIR__.'/../../public/profiles/'.$user->getId(), $newName);

            $user->setProfilePic($photoFolder.'/'.$user->getId().'/'.$newName);
            $em->flush();
        }
        return new Response();
    }

     /**
     * @Route("/search", name="search", methods={"POST"})
     */
    public function search(Request $request){
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        $response = new JsonResponse([]);

        $username = $request->request->get('username');
        $user = $uRepository->findBy(array('username'=>$username));
        if($user){
            $response->setData($user);
        }
        return $response;
    }

    /**
     * @Route("/follow", name="follow", methods={"POST"})
     */
    public function follow(Request $request){
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        $response = new JsonResponse([]);
        
        $token = $request->headers->get('Authorization');
        $user = $uRepository->findOneByToken($token);
        $followedUser = $uRepository->find($request->request->get('followedUserId'));

        if($followedUser){
            $user->addFollowing($followedUser);
            $em->flush();
        }
        return $response;
    }

    /**
     * @Route("/feed", name="get_feed", methods={"GET"})
     */
    public function getFeed(Request $request){
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        
        $token = $request->headers->get('Authorization');
        $user = $uRepository->findOneByToken($token);
        $following = $user->getFollowing()->toArray();
        usort($following, function($a, $b) {
            if($a->getLastActivityAt() == null){
                return -1;
            }
            if($b->getLastActivityAt() == null){
                return 1;
            }
            return ($a->getLastActivityAt() < $b->getLastActivityAt()) ? -1 : 1;
        });
        $lastFollowing = array_slice($following, 0, 10);
        foreach($lastFollowing as $user){
            $user->getLastActivityAt()->now = new \DateTime();
        }

        return new JsonResponse($lastFollowing);
    }

    
    /**
     * @Route("/fcm_refresh", name="fcm_refresh_token", methods={"POST"})
     */
    public function refreshFcmToken(Request $request){        
        $uRepository = $em->getRepository(User::class);
        $token = $request->headers->get('Authorization');
        $user = $uRepository->findOneByToken($token);

        $user =  Auth::user();
        $fcm_token = $request->input("fcm_token");
        if(!is_null($fcm_token)){
            $user->setFcmToken($fcm_token);
            $user->save();
        }

        $response = new JsonResponse();
        $response->setData(array("success"=>true));
        return $response;
    }

    /**
     * @Route("/getAbos", name="get_abos", methods={"GET"})
     */
    public function getAbos(Request $request){ 
        $response = new JsonResponse();
        $response->setData(array("success"=>true));
        return $response;
    }

    /**
     * @Route("/setAbos", name="set_abos", methods={"POST"})
     */
    public function setAbos(Request $request){ 
        $response = new JsonResponse();

        $file = fopen('abos.txt', 'a');
        $all = file_get_contents('abos.txt');
        $rawdata = $request->request->get("data");
        $matches = [];
        preg_match_all('/"node":((?!node).)*"id":"([^"]*)"((?!node).)*("username":"[^"]*")((?!node).)*}/', $rawdata, $matches);
        foreach($matches[2] as $key=>$value){
            if(strpos($all, $value) === false) {
                fwrite($file, $value."\n");
                $all .= '-'.$value.'-';
            }
        }
        fclose($file);

        $response->setData(array("success"=>true));
        return $response;
    }

    // A APPELER SEULEMENT LORS D'UN DESABONNEMENT
    // Peut contenir des ids en trop, si la requete est ensuite rejetée en 403 par instagram
    /**
     * @Route("/isAbo/{id}", requirements={"id"="\d+"},  name="is_abo", methods={"GET"})
     */
    public function isAbo(Request $request, $id){ 
        $response = new JsonResponse();

        $file = fopen('abos.txt', 'r');
        $content = stream_get_contents($file);
        $currentSubs = explode("\n", $content);
        $doesFollowMe = in_array($id, $currentSubs);
        // On ajoute a la liste des abos car cette route n'est censée etre appelée que lors d'un désabo
        if(!$doesFollowMe){
            $file = fopen('desabos.txt', 'a');
            fwrite($file, $id."\n");
            fclose($file);
        }
        // Si non abo, on interdit le désabo
        $response->setData(array("follows_me"=>$doesFollowMe));
        return $response;
    }

    /**
     * @Route("/isDesabo/{id}", requirements={"id"="\d+"},  name="is_desabo", methods={"GET"})
     */
    public function isDesabo(Request $request, $id){
        $file = fopen('desabos.txt', 'r');
        $content = stream_get_contents($file);
        $unfollowedList = explode("\n", $content);
        $unfollowed_once = in_array($id, $unfollowedList);

        // Si deja abo avant, et unfollow, on interdit un nouvel abo ou demande d'abo
        $response = new JsonResponse();
        $response->setData(array("unfollowed_once"=>$unfollowed_once));
        return $response;
    }


    // UTILS
    function randomString(
        $length,
        $set = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
        $repeat = 10
    ) {
        return substr(str_shuffle(str_repeat($set, $repeat)), 0, $length);
    }


}
