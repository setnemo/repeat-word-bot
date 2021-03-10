<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use RepeatBot\Core\ORM\Entities\Training;

/**
 * Class TrainingCollection
 * @package RepeatBot\Core\ORM\Collections
 */
class TrainingCollection extends ArrayCollection
{
    /**
     * @return Training|null
     */
    public function getRandomEntity(): ?Training
    {
        return $this->get(mt_rand(0, $this->count()));
    }
}
