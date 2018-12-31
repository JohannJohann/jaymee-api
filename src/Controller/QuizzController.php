<?php

namespace App\Controller;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: authorization");

use App\Entity\Quizz;
use App\Form\QuizzType;
use App\Repository\QuizzRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use App\Entity\MediaKey;


/**
 * @Route("/quizz")
 */
class QuizzController extends AbstractController
{
    /**
     * @Route("/", name="quizz_index", methods="GET")
     */
    public function index(QuizzRepository $quizzRepository): Response
    {
        return $this->render('quizz/index.html.twig', ['quizzs' => $quizzRepository->findAll()]);
    }

    /**
     * @Route("/new", name="create_quizz", methods="POST")
     */
    public function new(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        $user = $uRepository->findOneByToken($request->headers->get('Authorization'));
        $answers = explode(',', $request->request->get('answers'));

        $quizz = new Quizz();
        $quizz->setOwner($user);
        $quizz->setQuestion($request->request->get('question'));
        $quizz->setCorrectAnswer($answers[0]);
        shuffle($answers);
        $quizz->setChoices($answers);
        $quizz->setCreatedAt(new \DateTime());
        $quizz->setAttempts(0);
        $quizz->setSuccesses(0);

        $em->persist($quizz);
        $em->flush();

        return new JsonResponse($quizz->getId());
    }

    /**
     * @Route("/last", name="get_last_quizzes", methods="GET")
     */
    public function getLast(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        $qRepository = $em->getRepository(Quizz::class);
        $user = $uRepository->findOneByToken($request->headers->get('Authorization'));

        $quizz = $qRepository->getLast($user);

        return new JsonResponse($quizz);
    }
    
    /**
     * @Route("/{user_id}", name="get_random_quizz_for_user", methods="GET")
     */
    public function getRandomQuizz(Request $request, $user_id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        $qRepository = $em->getRepository(Quizz::class);
        $user = $uRepository->findOneByToken($request->headers->get('Authorization'));
        $from = $uRepository->find($user_id);

        if( $from ) {
            $quizz = $qRepository->getRandom($user, $from);
            $quizz->getOwner();
            return new JsonResponse($quizz);
        } else {
            return new JsonResponse();
        }
    }

    /**
     * @Route("/answer", name="check_answer", methods="POST")
     */
    public function checkAnswer(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $uRepository = $em->getRepository(User::class);
        $qRepository = $em->getRepository(Quizz::class);
        $user = $uRepository->findOneByToken($request->headers->get('Authorization'));

        $quizz = $qRepository->find($request->request->get('quizzId'));
        $quizz->addAnsweredBy($user);
        $quizz->setAttempts($quizz->getAttempts() + 1);

        $isCorrectAnswer = $quizz->getCorrectANswer() == $request->request->get('answer');

        if($isCorrectAnswer){
            $quizz->setSuccesses($quizz->getSuccesses() + 1);

            $existingKey = $quizz->getOwner()->getSharedKeys()->filter(function(MediaKey $key) use ($user) {
                return $key->getOwner() == $user;
            });
            if(!$existingKey->isEmpty()){
                $matchingKey = $existingKey->first();
                $matchingKey->setQuantity( $matchingKey->getQuantity() + 5 );
            } else {
                $key = new MediaKey();
                $key->setTarget($quizz->getOwner());
                $key->setQuantity(5);
                $em->persist($key);
                $user->addUnlockedKey($key);
            }
        }
        $em->flush();
        return new JsonResponse(array('success' => $isCorrectAnswer));
    }


}
