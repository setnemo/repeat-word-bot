<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Repositories;

use Doctrine\ORM\EntityRepository;
use RepeatBot\Core\ORM\Collections\WordCollection;
use RepeatBot\Core\ORM\Entities\Word;

class WordRepository extends EntityRepository
{
    /**
     * @param int $collectionId
     *
     * @return WordCollection
     */
    public function getWordsByCollectionId(int $collectionId): WordCollection
    {
        return new WordCollection($this->findBy(['collectionId' => $collectionId]));
    }

    /**
     * @param int $collectionId
     *
     * @return array
     */
    public function getExampleWords(int $collectionId): array
    {
        $result = $this->findBy(['collectionId' => $collectionId]);
        shuffle($result);

        $cutted = array_slice($result, 0, 30);

        $response = [];

        foreach ($cutted as $item) {
            $response = array_merge($response, [$item->getWord()]);
        }

        return $response;
    }
}
