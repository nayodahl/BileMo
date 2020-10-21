<?php

namespace App\Repository;

use App\Entity\SigninAttempt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SigninAttempt|null find($id, $lockMode = null, $lockVersion = null)
 * @method SigninAttempt|null findOneBy(array $criteria, array $orderBy = null)
 * @method SigninAttempt[]    findAll()
 * @method SigninAttempt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SigninAttemptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SigninAttempt::class);
    }

    public function countRecentSigninAttempts(string $ipAddress, int $delay): int
    {
        $timeAgo = new \DateTimeImmutable(sprintf('-%d minutes', $delay));

        return $this->createQueryBuilder('sa')
            ->select('COUNT(sa)')
            ->where('sa.date >= :date')
            ->andWhere('sa.ipAddress = :ipAddress')
            ->getQuery()
            ->setParameters([
                'date' => $timeAgo,
                'ipAddress' => $ipAddress,
            ])
            ->getSingleScalarResult()
        ;
    }
}
