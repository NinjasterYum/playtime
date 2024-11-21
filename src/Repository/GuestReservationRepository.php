<?php

namespace App\Repository;

use App\Entity\GuestReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GuestReservation>
 *
 * @method GuestReservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuestReservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuestReservation[]    findAll()
 * @method GuestReservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuestReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GuestReservation::class);
    }

    public function save(GuestReservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GuestReservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}