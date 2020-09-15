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

use HyperfExt\Enum\Rules\Enum;
use HyperfTest\Enum\Enums\UserType;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EnumValidationTest extends TestCase
{
    public function testValidationPasses()
    {
        $passes1 = (new Enum(UserType::class))->passes('', UserType::Administrator());

        $this->assertTrue($passes1);
    }

    public function testValidationFails()
    {
        $fails1 = (new Enum(UserType::class))->passes('', 'Some string');
        $fails2 = (new Enum(UserType::class))->passes('', 1);
        $fails3 = (new Enum(UserType::class))->passes('', UserType::Administrator()->key);
        $fails4 = (new Enum(UserType::class))->passes('', UserType::Administrator()->value);

        $this->assertFalse($fails1);
        $this->assertFalse($fails2);
        $this->assertFalse($fails3);
        $this->assertFalse($fails4);
    }

    public function testAnExceptionIsThrownIfAnNonExistingClassIsPassed()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Enum('PathToAClassThatDoesntExist'))->passes('', 'Test');
    }

    public function testCanSerializeToString()
    {
        $rule = new Enum(UserType::class);

        $this->assertEquals('enum:' . UserType::class, (string) $rule);
    }
}
