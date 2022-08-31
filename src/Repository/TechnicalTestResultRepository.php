<?php

namespace App\Repository;

use App\Entity\TechnicalTestResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TechnicalTestResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method TechnicalTestResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method TechnicalTestResult[]    findAll()
 * @method TechnicalTestResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TechnicalTestResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TechnicalTestResult::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TechnicalTestResult $entity, bool $flush = true): void
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
    public function remove(TechnicalTestResult $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return TechnicalTestResult[] Returns an array of TechnicalTestResult objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TechnicalTestResult
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
