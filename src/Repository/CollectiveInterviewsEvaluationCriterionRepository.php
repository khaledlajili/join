<?php

namespace App\Repository;

use App\Entity\CollectiveInterviewsEvaluationCriterion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CollectiveInterviewsEvaluationCriterion|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectiveInterviewsEvaluationCriterion|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectiveInterviewsEvaluationCriterion[]    findAll()
 * @method CollectiveInterviewsEvaluationCriterion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectiveInterviewsEvaluationCriterionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectiveInterviewsEvaluationCriterion::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CollectiveInterviewsEvaluationCriterion $entity, bool $flush = true): void
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
    public function remove(CollectiveInterviewsEvaluationCriterion $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return CollectiveInterviewsEvaluationCriterion[] Returns an array of CollectiveInterviewsEvaluationCriterion objects
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
    public function findOneBySomeField($value): ?CollectiveInterviewsEvaluationCriterion
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
