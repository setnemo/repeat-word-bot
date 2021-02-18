<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Helpers\DatabaseTestCase;

/**
 * Class DatabaseConnectionTest
 * @package Tests\Unit
 */
final class DatabaseConnectionTest extends DatabaseTestCase
{
    public function testConnection(): void
    {
        $this->getConnection();
        $this->assertEquals(2, 1 + 1);
    }
}
