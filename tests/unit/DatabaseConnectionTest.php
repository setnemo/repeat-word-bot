<?php

declare(strict_types=1);

namespace Tests\Unit;

use RepeatBot\Core\ORM\Entities\User;
use Tests\Helpers\DatabaseTestCase;

/**
 * Class DatabaseConnectionTest
 * @package Tests\Unit
 */
final class DatabaseConnectionTest extends DatabaseTestCase
{
    public function testConnection(): void
    {
        $repo = $this->getEntityManager()->getRepository(User::class);
        $this->assertEquals(2, 1 + 1);
    }
}
