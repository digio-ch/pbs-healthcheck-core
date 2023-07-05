<?php

namespace App\Helper;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class BatchedRepository
{

    private ServiceEntityRepository $repository;

    private int $batchCount = 0;

    private int $batchSize;

    /**
     * @param ServiceEntityRepository $repository
     * @param int $batchCount
     * @param int $batchSize
     */
    public function __construct(ServiceEntityRepository $repository, int $batchSize = 500)
    {
        $this->repository = $repository;
        $this->batchSize = $batchSize;
    }

    public function add($entity)
    {
        $this->batchCount++;
        $this->repository->add($entity, false);
        if ($this->batchCount >= $this->batchSize) {
            $this->batchCount = 0;
            $this->flush();
        }
    }

    public function flush()
    {
        $this->repository->flush();
    }
}
