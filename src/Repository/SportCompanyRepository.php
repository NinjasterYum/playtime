<?php

namespace App\Repository;

use App\Entity\SportCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SportCompany>
 */
class SportCompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SportCompany::class);
    }

    public function findAllWithImages(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.images', 'i')
            ->addSelect('i')
            ->getQuery()
            ->getResult();
    }

    
    public function findBySearch($venue): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.name LIKE :venue')
            ->setParameter('venue', '%'.$venue.'%')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return SportCompany[] Returns an array of SportCompany objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SportCompany
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
