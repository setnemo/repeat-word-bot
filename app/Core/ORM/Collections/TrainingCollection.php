<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use RepeatBot\Core\Log;
use RepeatBot\Core\ORM\Entities\Training;

/**
 * Class TrainingCollection
 * @package RepeatBot\Core\ORM\Collections
 */
class TrainingCollection extends ArrayCollection
{
    /**
     * @return Training|null
     * @throws \Exception
     */
    public function getRandomEntity(): ?Training
    {
        $random = 0;
        try {
            $random = random_int(0, $this->count() - 1);
        } catch (\Exception $e) {
            Log::getInstance()->getLogger()->error('Fail random_int: ' . $e->getMessage());
        }

        return $this->get($random);
    }
}
