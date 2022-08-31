<?php

namespace App\Repository;

use App\Entity\CollectiveInterviewsCriterionResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CollectiveInterviewsCriterionResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectiveInterviewsCriterionResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectiveInterviewsCriterionResult[]    findAll()
 * @method CollectiveInterviewsCriterionResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectiveInterviewsCriterionResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectiveInterviewsCriterionResult::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CollectiveInterviewsCriterionResult $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(CollectiveInterviewsCriterionResult $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return CollectiveInterviewsCriterionResult[] Returns an array of CollectiveInterviewsCriterionResult objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CollectiveInterviewsCriterionResult
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
