<?php

namespace App\Repository;

use App\Entity\Filtre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Filtre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Filtre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Filtre[]    findAll()
 * @method Filtre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FiltreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Filtre::class);
    }

    // /**
    //  * @return Filtre[] Returns an array of Filtre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Filtre
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
