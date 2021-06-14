<?php

namespace App\Repository;

use App\Entity\Haircut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HaircutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Haircut::class); 
    }

    // /**
    //  * @return Haircut[] Returns an array of Service objects
    //  */
    
    public function haircutNumber($firstResult, $maxResult)
    {
        return $this->createQueryBuilder('c')
            //->select("c.id, c.name, c.image")
            ->setfirstResult($firstResult)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult();
    } 

    /**
     * Récupère une liste de commentaires triés et paginés.
     *
     * @param int $page         Le numéro de la
     *                          page
     * @param int $nbMaxParPage Nombre maximum de commentaire par page
     *
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException
     *
     * @return Paginator
     */
    public function findAllPage($page, $nbMaxParPage)
    {
        if (!is_numeric($page)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $page est incorrecte (valeur : ' . $page . ').'
            );
        }

        if ($page < 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas');
        }

        if (!is_numeric($nbMaxParPage)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $nbMaxParPage est incorrecte (valeur : ' . $nbMaxParPage . ').'
            );
        }
    
        $orm = $this->createQueryBuilder('abc')
            ->orderBy('abc.id', 'ASC');
   
        $query = $orm->getQuery();

        $premierResultat = ($page - 1) * $nbMaxParPage;
        $query->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
        $paginator = new Paginator($query);

        if (($paginator->count() <= $premierResultat) && $page != 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
        }

        return $paginator;
    }
    
    public function findOneBySomeField($value): ?Haircut
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }   
}
