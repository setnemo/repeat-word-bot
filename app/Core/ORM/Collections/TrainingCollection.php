<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use RepeatBot\Core\ORM\Entities\Training;

class TrainingCollection extends ArrayCollection
{
    public function getRandomEntity(): ?Training
    {
        return $this->get(mt_rand(0, $this->count()));
    }
}
