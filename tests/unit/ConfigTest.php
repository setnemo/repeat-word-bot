<?php

declare(strict_types=1);

namespace Tests\Unit;

use RepeatBot\Common\Config;
use Tests\Helpers\DatabaseTestCase;

/**
 * Class ConfigTest
 * @package Tests\Unit
 */
final class ConfigTest extends DatabaseTestCase
{
    public function testConfigPath1(): void
    {
        $array = [
            'test' => '1'
        ];
        $config = new Config($array);
        $this->assertEquals('1', $config->getKey('test'));
    }

    public function testConfigPath2(): void
    {
        $array = [
            'test' => [
                '2' => '1'
            ]
        ];
        $config = new Config($array);
        $this->assertEquals('1', $config->getKey('test.2'));
    }

    public function testConfigPath3(): void
    {
        $array = [
            'test' => [
                '2' => [
                    '3' => '1'
                ]
            ]
        ];
        $config = new Config($array);
        $this->assertEquals('1', $config->getKey('test.2.3'));
    }
}
