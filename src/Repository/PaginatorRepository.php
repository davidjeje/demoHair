<?php

namespace App\Repository;

use App\Entity\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Paginator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paginator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paginator[]    findAll()
 * @method Paginator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaginatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paginator::class);
    }

    // /**
    //  * @return Paginator[] Returns an array of Paginator objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Paginator
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
