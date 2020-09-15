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

use HyperfExt\Enum\Enum;
use HyperfTest\Enum\Enums\MixedKeyFormats;
use HyperfTest\Enum\Enums\StringValues;
use HyperfTest\Enum\Enums\UserType;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EnumTest extends TestCase
{
    public function testEnumValues()
    {
        $this->assertEquals(0, UserType::Administrator);
        $this->assertEquals(3, UserType::SuperAdministrator);
    }

    public function testEnumGetKeys()
    {
        $keys = UserType::getKeys();
        $expectedKeys = ['Administrator', 'Moderator', 'Subscriber', 'SuperAdministrator'];

        $this->assertEquals($expectedKeys, $keys);
    }

    public function testEnumCoerce()
    {
        $enum = UserType::coerce(UserType::Administrator()->value);
        $this->assertEquals(UserType::Administrator, $enum->value);

        $enum = UserType::coerce(UserType::Administrator()->key);
        $this->assertEquals(UserType::Administrator, $enum->value);

        $enum = UserType::coerce(-1);
        $this->assertEquals(null, $enum);

        $enum = UserType::coerce(null);
        $this->assertEquals(null, $enum);
    }

    public function testEnumGetValues()
    {
        $values = UserType::getValues();
        $expectedValues = [0, 1, 2, 3];

        $this->assertEquals($expectedValues, $values);
    }

    public function testEnumGetKey()
    {
        $this->assertEquals('Moderator', UserType::getKey(1));
        $this->assertEquals('SuperAdministrator', UserType::getKey(3));
    }

    public function testEnumGetKeyUsingStringValue()
    {
        $this->assertEquals('Administrator', StringValues::getKey('administrator'));
    }

    public function testEnumGetValue()
    {
        $this->assertEquals(1, UserType::getValue('Moderator'));
        $this->assertEquals(3, UserType::getValue('SuperAdministrator'));
    }

    public function testEnumGetValueUsingStringKey()
    {
        $this->assertEquals('administrator', StringValues::getValue('Administrator'));
    }

    public function testEnumGetDescription()
    {
        $this->assertEquals('Normal', MixedKeyFormats::getDescription(MixedKeyFormats::Normal));
        $this->assertEquals('Multi word key name', MixedKeyFormats::getDescription(MixedKeyFormats::MultiWordKeyName));
        $this->assertEquals('Uppercase', MixedKeyFormats::getDescription(MixedKeyFormats::UPPERCASE));
        $this->assertEquals('Uppercase snake case', MixedKeyFormats::getDescription(MixedKeyFormats::UPPERCASE_SNAKE_CASE));
        $this->assertEquals('Lowercase snake case', MixedKeyFormats::getDescription(MixedKeyFormats::lowercase_snake_case));
    }

    public function testEnumGetRandomKey()
    {
        $this->assertContains(UserType::getRandomKey(), UserType::getKeys());
    }

    public function testEnumGetRandomValue()
    {
        $this->assertContains(UserType::getRandomValue(), UserType::getValues());
    }

    public function testEnumToArray()
    {
        $array = UserType::asArray();
        $expectedArray = [
            'Administrator' => 0,
            'Moderator' => 1,
            'Subscriber' => 2,
            'SuperAdministrator' => 3,
        ];

        $this->assertEquals($expectedArray, $array);
    }

    public function testEnumAsSelectArray()
    {
        $array = UserType::asSelectArray();
        $expectedArray = [
            0 => 'Administrator',
            1 => 'Moderator',
            2 => 'Subscriber',
            3 => 'Super administrator',
        ];

        $this->assertEquals($expectedArray, $array);
    }

    public function testEnumAsSelectArrayWithStringValues()
    {
        $array = StringValues::asSelectArray();
        $expectedArray = [
            'administrator' => 'Administrator',
            'moderator' => 'Moderator',
        ];

        $this->assertEquals($expectedArray, $array);
    }

    public function testEnumIsMacroableWithStaticMethods()
    {
        Enum::macro('asFlippedArray', function () {
            return array_flip(self::asArray());
        });

        $this->assertTrue(UserType::hasMacro('asFlippedArray'));
        $this->assertEquals(UserType::asFlippedArray(), array_flip(UserType::asArray()));
    }

    public function testEnumIsMacroableWithInstanceMethods()
    {
        Enum::macro('macroGetValue', function () {
            return $this->value;
        });

        $this->assertTrue(UserType::hasMacro('macroGetValue'));

        $user = new UserType(UserType::Administrator);
        $this->assertSame(UserType::Administrator, $user->macroGetValue());
    }

    public function testEnumGetInstances()
    {
        /** @var StringValues $administrator */
        /** @var StringValues $moderator */
        [
            'Administrator' => $administrator,
            'Moderator' => $moderator
        ] = StringValues::getInstances();

        $this->assertTrue(
            $administrator->is(StringValues::Administrator)
        );

        $this->assertTrue(
            $moderator->is(StringValues::Moderator)
        );
    }

    public function testEnumCanBeCastToString()
    {
        $enumWithZeroIntegerValue = new UserType(UserType::Administrator);
        $enumWithPositiveIntegerValue = new UserType(UserType::SuperAdministrator);
        $enumWithStringValue = new StringValues(StringValues::Moderator);

        // Numbers should be cast to strings
        $this->assertSame('0', (string) $enumWithZeroIntegerValue);
        $this->assertSame('3', (string) $enumWithPositiveIntegerValue);

        // Strings should just be returned
        $this->assertSame(StringValues::Moderator, (string) $enumWithStringValue);
    }

    public function testEnumCanBeJsonEncoded()
    {
        $this->assertEquals('1', json_encode(UserType::Moderator()));
    }
}
