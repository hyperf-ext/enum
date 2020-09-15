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

use HyperfExt\Enum\Rules\EnumValue;
use HyperfTest\Enum\Enums\StringValues;
use HyperfTest\Enum\Enums\SuperPowers;
use HyperfTest\Enum\Enums\UserType;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EnumValueTest extends TestCase
{
    public function testValidationPasses()
    {
        $passes1 = (new EnumValue(UserType::class))->passes('', 3);
        $passes2 = (new EnumValue(StringValues::class))->passes('', 'administrator');

        $this->assertTrue($passes1);
        $this->assertTrue($passes2);
    }

    public function testValidationFails()
    {
        $fails1 = (new EnumValue(UserType::class))->passes('', 7);
        $fails2 = (new EnumValue(UserType::class))->passes('', 'OtherString');
        $fails3 = (new EnumValue(UserType::class))->passes('', '3');

        $this->assertFalse($fails1);
        $this->assertFalse($fails2);
        $this->assertFalse($fails3);
    }

    public function testFlaggedEnumPassesWithNoFlagsSet()
    {
        $passed = (new EnumValue(SuperPowers::class))->passes('', 0);

        $this->assertTrue($passed);
    }

    public function testFlaggedEnumPassesWithSingleFlagSet()
    {
        $passed = (new EnumValue(SuperPowers::class))->passes('', SuperPowers::Flight);

        $this->assertTrue($passed);
    }

    public function testFlaggedEnumPassesWithMultipleFlagsSet()
    {
        $passed = (new EnumValue(SuperPowers::class))->passes('', SuperPowers::Superman);

        $this->assertTrue($passed);
    }

    public function testFlaggedEnumPassesWithAllFlagsSet()
    {
        $allFlags = array_reduce(SuperPowers::getValues(), function (int $carry, int $powerValue) {
            return $carry | $powerValue;
        }, 0);
        $passed = (new EnumValue(SuperPowers::class))->passes('', $allFlags);

        $this->assertTrue($passed);
    }

    public function testFlaggedEnumFailsWithInvalidFlagSet()
    {
        $allFlagsSet = array_reduce(SuperPowers::getValues(), function ($carry, $value) {
            return $carry | $value;
        }, 0);
        $passed = (new EnumValue(SuperPowers::class))->passes('', $allFlagsSet + 1);

        $this->assertFalse($passed);
    }

    public function testCanTurnOffStrictTypeChecking()
    {
        $passes = (new EnumValue(UserType::class, false))->passes('', '3');

        $this->assertTrue($passes);

        $fails1 = (new EnumValue(UserType::class, false))->passes('', '10');
        $fails2 = (new EnumValue(UserType::class, false))->passes('', 'a');

        $this->assertFalse($fails1);
        $this->assertFalse($fails2);
    }

    public function testAnExceptionIsThrownIfAnNonExistingClassIsPassed()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new EnumValue('PathToAClassThatDoesntExist'))->passes('', 'Test');
    }

    public function testCanSerializeToStringWithoutStrictTypeChecking()
    {
        $rule = new EnumValue(UserType::class, false);

        $this->assertEquals('enum_value:' . UserType::class . ',false', (string) $rule);
    }

    public function testCanSerializeToStringWithStrictTypeChecking()
    {
        $rule = new EnumValue(UserType::class, true);

        $this->assertEquals('enum_value:' . UserType::class . ',true', (string) $rule);
    }
}
