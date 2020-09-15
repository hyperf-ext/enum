<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfTest\Enum;

use HyperfTest\Enum\Enums\SingleValue;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EnumRandomTest extends TestCase
{
    public function testCanGetRandomKey()
    {
        $key = SingleValue::getRandomKey();

        $this->assertSame(
            SingleValue::getKey(SingleValue::KEY),
            $key
        );
    }

    public function testCanGetRandomValue()
    {
        $value = SingleValue::getRandomValue();

        $this->assertSame(SingleValue::KEY, $value);
    }

    public function testCanGetRandomInstance()
    {
        $instance = SingleValue::getRandomInstance();

        $this->assertTrue(
            $instance->is(SingleValue::KEY)
        );
    }
}
