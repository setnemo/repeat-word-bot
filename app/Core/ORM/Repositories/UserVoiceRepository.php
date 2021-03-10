<?php

declare(strict_types=1);

namespace RepeatBot\Core\ORM\Repositories;

use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Core\ORM\Entities\UserVoice;

/**
 * Class UserVoiceRepository
 * @package RepeatBot\Core\ORM\Repositories
 */
class UserVoiceRepository extends EntityRepository
{
    const DEFAULT_VOICE = 'en-US-Wavenet-A';

    /**
     * @param int $userId
     *
     * @return array
     */
    public function getFormattedVoices(int $userId): array
    {
        $resultVoices = [];
        $result = $this->findBy(['userId' => $userId]);
        if ($result !== null) {
            /** @var UserVoice $voice */
            foreach ($result as $voice) {
                $resultVoices[$voice->getVoice()] = $voice->getUsed();
            }
        }

        $keys = array_keys($resultVoices);
        /** @var string $voice */
        foreach (BotHelper::getVoices() as $voice) {
            if (!in_array($voice, $keys)) {
                $resultVoices[$voice] = 0;
            }
        }
        return $resultVoices;
    }

    /**
     * @param int    $userId
     * @param string $voice
     * @param int    $used
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateUserVoice(int $userId, string $voice, int $used): void
    {
        /** @var UserVoice $entity */
        $entity = $this->findOneBy(['voice' => $voice, 'userId' => $userId]);
        if ($entity) {
            $entity->setUsed($used);
            $entity->setUpdatedAt(Carbon::now('Europe/Kiev'));
        } else {
            $entity = new UserVoice();
            $entity->setUserId($userId);
            $entity->setVoice($voice);
            $entity->setUsed($used);
            $entity->setCreatedAt(Carbon::now('Europe/Kiev'));
            $entity->setUpdatedAt(Carbon::now('Europe/Kiev'));
        }
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $userId
     *
     * @return string
     */
    public function getRandomVoice(int $userId): string
    {
        $results = $this->findBy(['userId' => $userId, 'used' => 1]);

        if ($results === []) {
            return self::DEFAULT_VOICE;
        }
        $random = mt_rand(0, count($results));

        return $results[$random]->getVoice();
    }
}
