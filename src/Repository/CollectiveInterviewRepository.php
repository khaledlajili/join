<?php

namespace App\Repository;

use App\Entity\CollectiveInterview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CollectiveInterview|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectiveInterview|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectiveInterview[]    findAll()
 * @method CollectiveInterview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectiveInterviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectiveInterview::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CollectiveInterview $entity, bool $flush = true): void
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
    public function remove(CollectiveInterview $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return CollectiveInterview[] Returns an array of CollectiveInterview objects
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
    public function findOneBySomeField($value): ?CollectiveInterview
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
