<?php

namespace App\Entity\Aggregated;

use App\Repository\Aggregated\AggregatedDateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AggregatedDateRepository::class)
 * @ORM\Table(name="hc_aggregated_date")
 */
class AggregatedDate extends AggregatedEntity
{
}
