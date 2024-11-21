<?php

namespace App\Repository;

use App\Entity\Terrain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Terrain>
 *
 * @method Terrain|null find($id, $lockMode = null, $lockVersion = null)
 * @method Terrain|null findOneBy(array $criteria, array $orderBy = null)
 * @method Terrain[]    findAll()
 * @method Terrain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TerrainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Terrain::class);
    }

    public function save(Terrain $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Terrain $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}