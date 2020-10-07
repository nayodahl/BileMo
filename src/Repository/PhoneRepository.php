<?php

namespace App\Repository;

use App\Entity\Phone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Phone|null find($id, $lockMode = null, $lockVersion = null)
 * @method Phone|null findOneBy(array $criteria, array $orderBy = null)
 * @method Phone[]    findAll()
 * @method Phone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Phone::class);
    }

    /**
     * get all phones, with pagination.
     */
    public function findAllPhones(int $page, int $limit): ?Paginator
    {
        $query = $this->createQueryBuilder('p')
        ->getQuery()
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit)
        ;

        return new Paginator($query);
    }
}
