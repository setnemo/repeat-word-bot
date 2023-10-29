<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Core\ORM\Collections\WordCollection;

/**
 * Class WordRepository
 * @package RepeatBot\Core\ORM\Repositories
 */
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

        $cut = array_slice($result, 0, 30);

        $response = [];

        foreach ($cut as $item) {
            $response = array_merge($response, [$item->getWord()]);
        }

        return $response;
    }

    /**
     * @param int $lastId
     *
     * @return WordCollection
     */
    public function getWordsForTranslate(int $lastId): WordCollection
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('w')
            ->from('RepeatBot\Core\ORM\Entities\Word', 'w')
            ->where('w.id > :id')
            ->orderBy('w.id', 'ASC')
            ->setParameter('id', $lastId);

        return new WordCollection($query->getQuery()->getResult());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function updateWord(int $id, string $newTranslate): void
    {
        $entity = $this->find($id);
        $entity->setTranslate($newTranslate);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}
