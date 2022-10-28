<?php

namespace App\Entity\aggregated;

use App\Repository\WidgetDateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WidgetDateRepository::class)
 * @ORM\Table(name="hc_aggregated_date")
 */
class AggregatedDate extends AggregatedData
{
}
