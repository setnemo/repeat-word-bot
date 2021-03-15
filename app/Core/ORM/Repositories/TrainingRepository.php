<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Repositories;

use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\Database;
use RepeatBot\Core\Exception\EmptyVocabularyException;
use RepeatBot\Core\ORM\Collections\InactiveUserCollection;
use RepeatBot\Core\ORM\Collections\TrainingCollection;
use RepeatBot\Core\ORM\Collections\UserNotificationCollection;
use RepeatBot\Core\ORM\Collections\WordCollection;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Entities\Word;
use RepeatBot\Core\ORM\ValueObjects\InactiveUser;

/**
 * Class TrainingRepository
 * @package RepeatBot\Core\ORM\Repositories
 */
class TrainingRepository extends EntityRepository
{
    public const ALWAYS_SILENT_MESSAGE = 1;

    /**
     * @param int $collectionId
     * @param int $userId
     *
     * @return bool
     */
    public function userHaveCollection(int $collectionId, int $userId): bool
    {
        return [] !== $this->findBy(['collectionId' => $collectionId, 'userId' => $userId]);
    }

    /**
     * @param WordCollection $words
     * @param string         $type
     * @param int            $userId
     *
     * @return int
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function bulkCreateTraining(
        WordCollection $words,
        string $type,
        int $userId
    ): int {
        $i = 0;
        /** @var Word $word */
        foreach ($words as $word) {
            try {
                $entity = new Training();
                $entity->setWordId($word->getId());
                $entity->setUserId($userId);
                $entity->setType($type);
                $entity->setWord($word);
                $entity->setCollectionId($word->getCollectionId());
                $entity->setStatus('first');
                $entity->setNext(Carbon::now(Database::DEFAULT_TZ));
                $entity->setCreatedAt(Carbon::now(Database::DEFAULT_TZ));
                $entity->setUpdatedAt(Carbon::now(Database::DEFAULT_TZ));
                $this->getEntityManager()->persist($entity);

                ++$i;
            } catch (\Throwable $t) {
            }
        }
        $this->getEntityManager()->flush();

        return $i;
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return TrainingCollection
     */
    public function getTrainingsByUserIdAndType(int $userId, string $type): TrainingCollection
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from('RepeatBot\Core\ORM\Entities\Training', 't')
            ->innerJoin(
                'RepeatBot\Core\ORM\Entities\Word',
                'w',
                Join::WITH,
                'w.id = t.wordId'
            )
            ->where('t.userId = :userId')
            ->andWhere('t.type = :type')
            ->andWhere('t.next < :next')
            ->setParameter('userId', $userId)
            ->setParameter('type', $type)
            ->setParameter('next', Carbon::now(Database::DEFAULT_TZ));

        return new TrainingCollection($query->getQuery()->getResult());
    }

    /**
     * @param int     $userId
     * @param ?string $type
     * @param bool    $priority
     *
     * @return Training
     * @throws EmptyVocabularyException
     */
    public function getRandomTraining(int $userId, ?string $type, bool $priority): Training
    {
        if (null == $type) {
            throw new EmptyVocabularyException();
        }

        $result = $this->getTrainingsByUserIdAndType($userId, $type);

        $ret = $this->getRandomEntity($priority, $result);

        if (!$ret) {
            throw new EmptyVocabularyException();
        }

        return $ret;
    }

    /**
     * @param int $trainingId
     *
     * @return Training
     */
    public function getTraining(int $trainingId): Training
    {
        return $this->findOneBy(['id' => $trainingId]);
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return TrainingCollection
     */
    public function getTrainings(int $userId, string $type): TrainingCollection
    {
        return new TrainingCollection($this->findBy(['userId' => $userId, 'type' => $type]));
    }

    /**
     * @param Training $training
     * @param bool     $never
     *
     * @return Training
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function upStatusTraining(Training $training, bool $never = false): Training
    {
        $newStatus = BotHelper::getNewStatus($training, $never);

        $training->setStatus($newStatus['status']);
        $training->setNext(Carbon::now(Database::DEFAULT_TZ)->addMinutes($newStatus['repeat']));
        $training->setUpdatedAt(Carbon::now(Database::DEFAULT_TZ));
        $this->getEntityManager()->persist($training);
        $this->getEntityManager()->flush();

        return $training;
    }

    /**
     * @param int $userId
     */
    public function removeAllTrainings(int $userId): void
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->delete('RepeatBot\Core\ORM\Entities\Training', 't')
            ->where('t.userId = :userId')
            ->setParameter('userId', $userId);

        $query->getQuery()->execute();
    }

    /**
     * @param int $userId
     */
    public function removeTrainings(int $userId, int $collectionId): void
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->delete('RepeatBot\Core\ORM\Entities\Training', 't')
            ->where('t.userId = :userId')
            ->andWhere('t.collectionId = :collectionId')
            ->setParameter('userId', $userId)
            ->setParameter('collectionId', $collectionId);

        $query->getQuery()->execute();
    }

    /**
     * @param int $userId
     * @param int $collectionId
     */
    public function resetTrainings(int $userId, int $collectionId): void
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->update('RepeatBot\Core\ORM\Entities\Training', 't')
            ->set('t.status', '?0')
            ->set('t.next', '?1')
            ->set('t.updatedAt', '?2')
            ->where('t.userId = :userId')
            ->andWhere('t.collectionId = :collectionId')
            ->setParameter('userId', $userId)
            ->setParameter('collectionId', $collectionId)
            ->setParameter(0, 'first')
            ->setParameter(1, Carbon::now(Database::DEFAULT_TZ))
            ->setParameter(2, Carbon::now(Database::DEFAULT_TZ));

        $query->getQuery()->execute();
    }

    /**
     * @param int $userId
     */
    public function resetAllTrainings(int $userId): void
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->update('RepeatBot\Core\ORM\Entities\Training', 't')
            ->set('t.status', '?0')
            ->set('t.next', '?1')
            ->set('t.updatedAt', '?2')
            ->where('t.userId = :userId')
            ->setParameter('userId', $userId)
            ->setParameter(0, 'first')
            ->setParameter(1, Carbon::now(Database::DEFAULT_TZ))
            ->setParameter(2, Carbon::now(Database::DEFAULT_TZ));

        $query->getQuery()->execute();
    }

    /**
     * @param UserNotificationCollection $userNotifications
     *
     * @return InactiveUserCollection
     */
    public function getInactiveUsers(UserNotificationCollection $userNotifications): InactiveUserCollection
    {
        $dql = "SELECT t, MAX(t.updatedAt) as max FROM RepeatBot\Core\ORM\Entities\Training t GROUP BY t.userId";
        $query = $this->getEntityManager()->createQuery($dql);

        $result = $query->getScalarResult();
        $ret = [];
        foreach ($result as $record) {
            if (strtotime($record['max']) < strtotime('-1 day')) {
                if (isset($userNotifications[$record['t_userId']])) {
                    if ($userNotifications[$record['t_userId']]->getDeleted() == 1) {
                        continue;
                    }
                }
                $ret[$record['t_userId']] =  new InactiveUser(
                    (int)$record['t_userId'],
                    isset($userNotifications[$record['t_userId']]) ?
                        $userNotifications[$record['t_userId']]->getSilent() :
                        self::ALWAYS_SILENT_MESSAGE,
                    $this->getMessageForInactiveUser()
                );
            }
        }

        return new InactiveUserCollection($ret);
    }

    /**
     * @param int    $userId
     * @param string $type
     *
     * @return Training
     * @throws EmptyVocabularyException
     */
    public function getNearestAvailableTrainingTime(int $userId, string $type): Training
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from('RepeatBot\Core\ORM\Entities\Training', 't')
            ->innerJoin(
                'RepeatBot\Core\ORM\Entities\Word',
                'w',
                Join::WITH,
                'w.id = t.wordId'
            )
            ->where('t.userId = :userId')
            ->andWhere('t.type = :type')
            ->setParameter('userId', $userId)
            ->setParameter('type', $type)
        ;
        $result = new TrainingCollection($query->getQuery()->getResult());
        if ($result->isEmpty()) {
            throw new EmptyVocabularyException();
        }

        return $result->get(0);
    }

    public function getMyStats(int $userId): array
    {
        $result = [];
        $types = BotHelper::getTrainingTypes();
        foreach ($types as $type) {
            $dql = "SELECT COUNT(t.status) as counter, t.status as status FROM RepeatBot\Core\ORM\Entities\Training t WHERE t.userId = ?1 AND t.type = ?2 GROUP BY t.status";
            $query = $this->getEntityManager()->createQuery($dql);
            $query->setParameter(1, $userId);
            $query->setParameter(2, $type);
            $resultType = $query->getScalarResult();
            $result[$type] = $resultType;
        }

        return $result;
    }

    /**
     * @param int    $userId
     * @param string $type
     * @param string $status
     *
     * @return TrainingCollection
     */
    public function getTrainingsWithStatus(int $userId, string $type, string $status): TrainingCollection
    {
        return new TrainingCollection($this->findBy(['userId' => $userId, 'type' => $type, 'status' => $status]));
    }

    /**
     * @return string
     */
    private function getMessageForInactiveUser(): string
    {
        return "Не останавливайся! Продолжи свою тренировку прямо сейчас!";
    }

    /**
     * @param bool               $priority
     * @param TrainingCollection $collection
     *
     * @return Training|null
     * @throws \Exception
     */
    private function getRandomEntity(bool $priority, TrainingCollection $collection): ?Training
    {
        if (!$priority) {
            $entity = $collection->getRandomEntity();
        } else {
            $rule = [
                'first' => 0,
                'second' => 1,
                'third' => 2,
                'fourth' => 3,
                'fifth' => 4,
                'sixth' => 5,
                'never' => 6,
            ];
            $tmp = [];
            foreach ($collection as $item) {
                $tmp[$rule[$item->getStatus()]][] = $item;
            }

            $tmpCollection = new TrainingCollection();
            for ($i = 0; $i < count($rule); ++$i) {
                if (!$tmpCollection->isEmpty()) {
                    break;
                }
                if (!empty($tmp[$i])) {
                    foreach ($tmp[$i] as $v) {
                        $tmpCollection->add($v);
                    }
                }
            }
            $entity = $tmpCollection->getRandomEntity();
        }

        return $entity;
    }
}
