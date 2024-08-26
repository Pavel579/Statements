<?php

namespace App\Repository;

use App\Entity\Statement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Statement>
 */
class StatementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Statement::class);
    }

    public function findByAuthorId(int $authorId): Statement
    {
        return $this->createQueryBuilder('s')
            ->where('s.author = :authorId')
            ->setParameter('authorId', $authorId)
            ->getQuery()
            ->getResult();
    }
}
