<?php

namespace App\Repository;

use App\Entity\InterviewsEvaluationCriterion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InterviewsEvaluationCriterion|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterviewsEvaluationCriterion|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterviewsEvaluationCriterion[]    findAll()
 * @method InterviewsEvaluationCriterion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterviewsEvaluationCriterionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterviewsEvaluationCriterion::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(InterviewsEvaluationCriterion $entity, bool $flush = true): void
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
    public function remove(InterviewsEvaluationCriterion $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return InterviewsEvaluationCriterion[] Returns an array of InterviewsEvaluationCriterion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InterviewsEvaluationCriterion
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
