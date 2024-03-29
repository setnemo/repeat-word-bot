<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Core\ORM\Repositories\TrainingRepository;

/**
 * Class OneYearService
 * @package RepeatBot\Bot\Service
 */
class OneYearService
{
    /**
     * OneYearService constructor.
     *
     * @param TrainingRepository $trainingRepository
     */
    public function __construct(private TrainingRepository $trainingRepository)
    {
    }

    /**
     * @param int $id
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function execute(int $id): void
    {
        $training = $this->trainingRepository->getTraining($id);
        $this->trainingRepository->upStatusTraining($training, true);
    }
}
