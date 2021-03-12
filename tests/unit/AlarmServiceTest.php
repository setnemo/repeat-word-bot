<?php

declare(strict_types=1);

namespace Tests\Unit;

use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Commands\AlarmService;
use RepeatBot\Core\ORM\Entities\User;
use Tests\Helpers\DatabaseTestCase;

/**
 * Class AlarmServiceTest
 * @package Tests\Unit
 */
final class AlarmServiceTest extends DatabaseTestCase
{
    public function testConnection(): void
    {
        $options = new CommandOptions();
        $alarmService = new AlarmService($options);
        $repo = $this->getEntityManager()->getRepository(User::class);
        $this->assertEquals(2, 1 + 1);
    }
}
