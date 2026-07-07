<?php

namespace App\Entity\Aggregated;

use App\Repository\Aggregated\AggregatedDateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_aggregated_date')]
#[ORM\Entity(repositoryClass: AggregatedDateRepository::class)]
class AggregatedDate extends AggregatedEntity
{
}
