<?php

namespace App\Repository;

use App\Entity\Quizz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Quizz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quizz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quizz[]    findAll()
 * @method Quizz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizzRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Quizz::class);
    }

    /**
     * @return Quizz[] Returns an array of Quizz objects - the last ones of the user
     */
    public function getLast($user) {
        return $this->createQueryBuilder('q')
        ->andWhere('q.owner = :user')
        ->setParameter('user', $user)
        ->orderBy('q.created_at', 'DESC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult()
        ;
    }

      /**
     * @return Quizz Returns a Quizz object - a random one for a given user from a given user
     * Both parameters are User objects
     */
    public function getRandom($for, $from) {
        $allQuizzes =  $this->createQueryBuilder('q')
        ->andWhere('q.owner = :from')
        ->setParameter('from', $from)
        ->getQuery()
        ->getResult();

        $filteredResults = array_filter($allQuizzes, function ($quizz) use ($for){
                return !$quizz->getAnsweredBy()->contains($for);
            });
        
        shuffle($filteredResults);
        
        if(empty($filteredResults)){
            return null;
        }
        return $filteredResults[0];
    }
}
