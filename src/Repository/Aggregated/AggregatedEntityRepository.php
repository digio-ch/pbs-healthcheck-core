<?php

namespace App\Repository\Aggregated;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AggregatedEntityRepository extends ServiceEntityRepository
{
    /**
     * @param $entity
     */
    public function remove($entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
