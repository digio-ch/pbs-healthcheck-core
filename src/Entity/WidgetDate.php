<?php

namespace App\Entity;

use App\Repository\WidgetDateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WidgetDateRepository::class)
 * @ORM\Table(name="hc_widget_date")
 */
class WidgetDate extends Widget
{
}
