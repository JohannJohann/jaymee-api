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

}
