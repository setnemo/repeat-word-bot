<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Repositories;

use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\Exception\EmptyVocabularyException;
use RepeatBot\Core\ORM\Collections\InactiveUserCollection;
use RepeatBot\Core\ORM\Collections\TrainingCollection;
use RepeatBot\Core\ORM\Collections\UserNotificationCollection;
use RepeatBot\Core\ORM\Collections\WordCollection;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Entities\Word;
use RepeatBot\Core\ORM\ValueObjects\InactiveUser;

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
     * @param Word   $word
     * @param string $type
     * @param int    $userId
     * @param bool   $push
     *
     * @return Training
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createTraining(
        Word $word,
        string $type,
        int $userId
    ): Training {
        $entity = new Training();
        $entity->setWordId($word->getId());
        $entity->setUserId($userId);
        $entity->setType($type);
        $entity->setWord($word);
        $entity->setCollectionId($word->getCollectionId());
        $entity->setStatus('first');
        $entity->setNext(Carbon::now('Europe/Kiev'));
        $entity->setCreatedAt(Carbon::now('Europe/Kiev'));
        $entity->setUpdatedAt(Carbon::now('Europe/Kiev'));

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     * @param Word   $word
     * @param string $type
     * @param int    $userId
     * @param bool   $push
     *
     * @return Training
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
                $entity->setNext(Carbon::now('Europe/Kiev'));
                $entity->setCreatedAt(Carbon::now('Europe/Kiev'));
                $entity->setUpdatedAt(Carbon::now('Europe/Kiev'));
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
     * @param bool   $priority
     *
     * @return Training
     * @throws EmptyVocabularyException
     */
    public function getRandomTraining(int $userId, string $type, bool $priority): Training
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
            ->setParameter('next', Carbon::now('Europe/Kiev'))
        ;
        $result = new TrainingCollection($query->getQuery()->getResult());

        if (!$priority) {
            $ret = $result->getRandomEntity();
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
            $ret2 = [];
            foreach ($result as $item) {
                $ret2[$rule[$item->getStatus()]][] = $item;
            }
            $ret = new TrainingCollection();
            for ($i = 0; $i < count($ret2); ++$i) {
                if (!empty($ret)) {
                    break ;
                }
                if (!empty($ret2[$i])) {
                    foreach ($ret2[$i] as $v) {
                        $ret->add($v);
                    }
                }
            }
            $ret = $ret->getRandomEntity();
        }

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

    public function upStatusTraining(Training $training, bool $never = false): Training
    {
        $newStatus = $this->getNewStatus($training, $never);

        $training->setStatus($newStatus['status']);
        $training->setNext(Carbon::now('Europe/Kiev')->addMinutes($newStatus['repeat']));
        $training->setUpdatedAt(Carbon::now('Europe/Kiev'));
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
            ->set('t.status', 'first')
            ->set('t.next', '?1')
            ->set('t.updatedAt', '?2')
            ->where('t.userId = :userId')
            ->andWhere('t.collectionId = :collectionId')
            ->setParameter('userId', $userId)
            ->setParameter('collectionId', $collectionId)
            ->setParameter(1, Carbon::now('Europe/Kiev'))
            ->setParameter(2, Carbon::now('Europe/Kiev'));

        $query->getQuery()->execute();
    }

    /**
     * @param int $userId
     */
    public function resetAllTrainings(int $userId): void
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->update('RepeatBot\Core\ORM\Entities\Training', 't')
            ->set('t.status', 'first')
            ->set('t.next', '?1')
            ->set('t.updatedAt', '?2')
            ->where('t.userId = :userId')
            ->setParameter('userId', $userId)
            ->setParameter(1, Carbon::now('Europe/Kiev'))
            ->setParameter(2, Carbon::now('Europe/Kiev'));

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
                if (isset($userNotifications[$record['user_id']])) {
                    if ($userNotifications[$record['user_id']]->getDeleted() == 1) {
                        continue;
                    }
                }
                $ret[$record['user_id']] =  new InactiveUser(
                    (int)$record['user_id'],
                    isset($userNotifications[$record['user_id']]) ?
                        $userNotifications[$record['user_id']]->getSilent() :
                        self::ALWAYS_SILENT_MESSAGE,
                    $this->getMessageForInactiveUser($record['user_id'])
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
        if (empty($result)) {
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
     * @param int    $type
     * @param string $status
     *
     * @return TrainingCollection
     */
    public function getTrainingsWithStatus(int $userId, string $type, string $status): TrainingCollection
    {
        return new TrainingCollection($this->findBy(['userId' => $userId, 'type' => $type, 'status' => $status]));
    }

    /**
     * @param int $userId
     *
     * @return string
     */
    private function getMessageForInactiveUser(int $userId): string
    {
        return "Не останавливайся! Продолжи свою тренировку прямо сейчас!";
    }

    /**
     * @param Training $training
     * @param bool     $never
     *
     * @return array
     */
    private function getNewStatus(Training $training, bool $never = false): array
    {
        $status = $never === false ? $training->getStatus() : 'never';

        return match($status) {
            'second' => [
                'status' => 'third',
                'repeat' => 3 * 24 * 60,
            ],
            'third' => [
                'status' => 'fourth',
                'repeat' => 7 * 24 * 60,
            ],
            'fourth' => [
                'status' => 'fifth',
                'repeat' => 30 * 24 * 60,
            ],
            'fifth' => [
                'status' => 'sixth',
                'repeat' => 90 * 24 * 60,
            ],
            'sixth' => [
                'status' => 'never',
                'repeat' => 180 * 24 * 60,
            ],
            'never' => [
                'status' => 'never',
                'repeat' => 360 * 24 * 60,
            ],
            default => [
                'status' => 'second',
                'repeat' => 24 * 60,
            ],
        };
    }
}
