<?php

namespace App\Repository;

use App\Entity\PreRegistrationFormFieldOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PreRegistrationFormFieldOption>
 *
 * @method PreRegistrationFormFieldOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method PreRegistrationFormFieldOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method PreRegistrationFormFieldOption[]    findAll()
 * @method PreRegistrationFormFieldOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreRegistrationFormFieldOptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PreRegistrationFormFieldOption::class);
    }

    public function add(PreRegistrationFormFieldOption $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PreRegistrationFormFieldOption $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PreRegistrationFormFieldOption[] Returns an array of PreRegistrationFormFieldOption objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PreRegistrationFormFieldOption
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
