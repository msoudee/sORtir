<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

     /**
      * @return Sortie[] Retourne un tableau de sorties de moins d'un mois, ordonnés par date de cloture
      */
    public function findByDateLimitOrderByDateCloture($dateLimit) {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateDebut >= :dateLimit')
            ->setParameter('dateLimit', $dateLimit)
            ->orderBy('s.dateCloture', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

}
