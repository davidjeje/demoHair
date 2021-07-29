<?php

namespace App\Repository;

use App\Entity\EventNotValidated;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EventNotValidated|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventNotValidated|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventNotValidated[]    findAll()
 * @method EventNotValidated[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventNotValidatedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventNotValidated::class);
    }

    // /**
    //  * @return EventNotValidated[] Returns an array of EventNotValidated objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EventNotValidated
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
