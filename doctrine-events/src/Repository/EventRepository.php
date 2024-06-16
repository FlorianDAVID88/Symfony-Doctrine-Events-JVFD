<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[] tous les events dans l'ordre de leur date
     */
    public function findNextEventsByDate(bool $isPublic): array
    {
        $qb = $this->createQueryBuilder('e');

        if ($isPublic === true) {
            $qb->andWhere('e.is_public = :isPublic')
                ->setParameter('isPublic', true);
        }

        return $qb->orderBy('e.datetime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getFromFilters($name = null, $dateMin = null, $dateMax = null, $isPublic = null, $isPrivate = null): array
    {
        $qb = $this->createQueryBuilder('e');

        if ($name) {
            $qb->andWhere('e.title LIKE :name')
                ->setParameter('name', '%' . $name . '%');
        }

        if ($dateMin && $dateMax) {
            $qb->andWhere('e.datetime BETWEEN :start AND :end')
                ->setParameter('start', $dateMin)
                ->setParameter('end', $dateMax);
        } elseif ($dateMin) {
            $qb->andWhere('e.datetime >= :start')
                ->setParameter('start', $dateMin);
        } elseif ($dateMax) {
            $qb->andWhere('e.datetime <= :end')
                ->setParameter('end', $dateMax);
        }

        if ($isPublic === 'on') {
            $qb->andWhere('e.is_public = TRUE');
        }

        if ($isPrivate === 'on') {
            $qb->andWhere('e.is_public = FALSE');
        }

        $qb->orderBy('e.datetime', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findAllPublicEvents(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.is_public = TRUE')
            ->orderBy('e.datetime', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
