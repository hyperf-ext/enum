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

use HyperfExt\Enum\Rules\EnumKey;
use HyperfTest\Enum\Enums\StringValues;
use HyperfTest\Enum\Enums\UserType;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EnumKeyTest extends TestCase
{
    public function testValidationPasses()
    {
        $passes1 = (new EnumKey(UserType::class))->passes('', 'Administrator');
        $passes2 = (new EnumKey(StringValues::class))->passes('', 'Administrator');
        $passes3 = (new EnumKey(StringValues::class))->passes('', 'administrator');

        $this->assertTrue($passes1);
        $this->assertTrue($passes2);
        $this->assertFalse($passes3);
    }

    public function testValidationFails()
    {
        $fails1 = (new EnumKey(UserType::class))->passes('', 'Anything else');
        $fails2 = (new EnumKey(UserType::class))->passes('', 2);
        $fails3 = (new EnumKey(UserType::class))->passes('', '2');

        $this->assertFalse($fails1);
        $this->assertFalse($fails2);
        $this->assertFalse($fails3);
    }

    public function testAnExceptionIsThrownIfAnNonExistingClassIsPassed()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new EnumKey('PathToAClassThatDoesntExist'))->passes('', 'Test');
    }

    public function testCanSerializeToString()
    {
        $rule = new EnumKey(UserType::class);

        $this->assertEquals('enum_key:' . UserType::class, (string) $rule);
    }
}
