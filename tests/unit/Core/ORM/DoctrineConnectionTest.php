<?php

declare(strict_types=1);

namespace Tests\Core;

use Codeception\Test\Unit;
use RepeatBot\Core\ORM\Entities\Version;
use UnitTester;

class DoctrineConnectionTest extends Unit
{
    protected UnitTester $tester;

    public function testBackupLoaded(): void
    {
        $em = $this->getModule('Doctrine2')->em;
        $version = $em->find(Version::class, 2);
        $this->tester->assertEquals('1.0.0', $version->getVersion());
    }
}
