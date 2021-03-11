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
        $r = mt_rand(0, $this->count());
        $a = $this->get($r);
        var_export([$r, $this->count()]);
    
        return $a;
    }
}
