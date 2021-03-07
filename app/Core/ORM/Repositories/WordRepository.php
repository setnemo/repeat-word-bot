<?php
declare(strict_types = 1);

namespace RepeatBot\Core\ORM\Repositories;

use Doctrine\ORM\EntityRepository;
use RepeatBot\Core\ORM\Collections\WordCollection;

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
     * @return WordCollection
     */
    public function getExampleWords(int $collectionId): WordCollection
    {
        $result = $this->findBy(['collectionId' => $collectionId]);
        shuffle($result);
        
        return new WordCollection(array_slice($result, 0, 30));
    }
}
