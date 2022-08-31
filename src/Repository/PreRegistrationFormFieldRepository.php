<?php

namespace App\Repository;

use App\Entity\PreRegistrationFormField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PreRegistrationFormField|null find($id, $lockMode = null, $lockVersion = null)
 * @method PreRegistrationFormField|null findOneBy(array $criteria, array $orderBy = null)
 * @method PreRegistrationFormField[]    findAll()
 * @method PreRegistrationFormField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreRegistrationFormFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PreRegistrationFormField::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PreRegistrationFormField $entity, bool $flush = true): void
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
    public function remove(PreRegistrationFormField $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return PreRegistrationFormField[] Returns an array of PreRegistrationFormField objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PreRegistrationFormField
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
