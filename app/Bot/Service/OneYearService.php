<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service;

use RepeatBot\Core\Database\Repository\TrainingRepository;

class OneYearService
{
    /**
     * @var TrainingRepository
     */
    private TrainingRepository $trainingRepository;

    /**
     * OneYearService constructor.
     *
     * @param TrainingRepository $trainingRepository
     */
    public function __construct(TrainingRepository $trainingRepository)
    {

        $this->trainingRepository = $trainingRepository;
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
