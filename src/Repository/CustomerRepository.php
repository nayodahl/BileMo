<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * Return all customers of a given Reseller.
     */
    public function findAllCustomersofOneReseller(int $resellerId)
    {
        return $this->createQueryBuilder('c')
        ->innerJoin('c.reseller', 'cr')->addSelect('cr')
        ->where('c.reseller = :resellerId')->setParameter('resellerId', $resellerId)
        ->getQuery()
        ;
    }

    /**
     * Return single customer of a given Reseller, or null.
     */
    public function findOneCustomerofOneReseller(int $resellerId, int $customerId): ?Customer
    {
        return $this->createQueryBuilder('c')
        ->innerJoin('c.reseller', 'cr')->addSelect('cr')
        ->where('c.reseller = :resellerId')->setParameter('resellerId', $resellerId)
        ->andWhere('c.id = :customerId')->setParameter('customerId', $customerId)
        ->getQuery()
        ->getOneOrNullResult()
        ;
    }

    /**
     * Return single customer of a given Reseller with a given email, or null.
     */
    public function findOneByEmailandReseller(int $resellerId, string $customerEmail): ?Customer
    {
        return $this->createQueryBuilder('c')
        ->innerJoin('c.reseller', 'cr')->addSelect('cr')
        ->where('c.reseller = :resellerId')->setParameter('resellerId', $resellerId)
        ->andWhere('c.email = :customerEmail')->setParameter('customerEmail', $customerEmail)
        ->getQuery()
        ->getOneOrNullResult()
        ;
    }
}
