<?php

namespace App\Repository;

use App\Entity\Reseller;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reseller|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reseller|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reseller[]    findAll()
 * @method Reseller[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResellerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reseller::class);
    }

    // /**
    //  * @return Reseller[] Returns an array of Reseller objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reseller
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
