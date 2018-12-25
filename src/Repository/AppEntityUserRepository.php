<?php

namespace App\Repository;

use App\Entity\AppEntityUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AppEntityUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppEntityUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppEntityUser[]    findAll()
 * @method AppEntityUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppEntityUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AppEntityUser::class);
    }

    // /**
    //  * @return AppEntityUser[] Returns an array of AppEntityUser objects
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
    public function findOneBySomeField($value): ?AppEntityUser
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
