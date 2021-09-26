<?php

namespace App\Service\PbsApi\Fetcher;

use App\Entity\Group;
use App\Entity\Person;
use App\Repository\GroupRepository;
use App\Repository\PersonRepository;
use App\Service\PbsApiService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;

abstract class AbstractFetcher
{
    /**
     * @var int
     */
    protected $batchSize = 100;
    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var PbsApiService
     */
    protected $pbsApiService;

    public function __construct(EntityManagerInterface $em, PbsApiService $pbsApiService) {
        $this->em = $em;
        $this->pbsApiService = $pbsApiService;
    }

    public function fetchAndPersist(string $groupId, string $accessToken)
    {
        $i = 0;
        foreach ($this->fetch($groupId, $accessToken) as $entity) {
            $this->em->persist($entity);
            $i++;

            if (($i % $this->batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }
        $this->em->flush();
    }

    protected abstract function fetch(string $groupId, string $accessToken): array;

    protected function getLinked(array $linked, string $rel, string $id) {
        return array_values(array_filter($linked[$rel] ?? [], function($linkedEntity) use ($id) {
            return $linkedEntity['id'] === $id;
        }))[0] ?? null;
    }
}
