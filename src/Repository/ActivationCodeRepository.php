<?php

namespace App\Repository;

use App\Entity\ActivationCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ActivationCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivationCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivationCode[]    findAll()
 * @method ActivationCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivationCodeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ActivationCode::class);
    }

    // /**
    //  * @return ActivationCode[] Returns an array of ActivationCode objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ActivationCode
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
