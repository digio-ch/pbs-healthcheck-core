<?php

declare(strict_types=1);

namespace App\Repository\Aggregated;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AggregatedEntityRepository extends ServiceEntityRepository
{
    /**
     * @param $entity
     */
    public function remove(object $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
