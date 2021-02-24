<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service;

use RepeatBot\Core\Database\Repository\TrainingRepository;

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
     */
    public function execute(int $id): void
    {
        $training = $this->trainingRepository->getTraining($id);
        $this->trainingRepository->upStatusTraining($training, true);
    }
}
