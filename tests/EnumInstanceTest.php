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

use HyperfExt\Enum\Exceptions\InvalidEnumKeyException;
use HyperfExt\Enum\Exceptions\InvalidEnumMemberException;
use HyperfTest\Enum\Enums\UserType;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EnumInstanceTest extends TestCase
{
    public function testCanInstantiateEnumClassWithNew()
    {
        $userType = new UserType(UserType::Administrator);
        $this->assertInstanceOf(UserType::class, $userType);
    }

    public function testCanInstantiateEnumClassFromValue()
    {
        $userType = UserType::fromValue(UserType::Administrator);
        $this->assertInstanceOf(UserType::class, $userType);
    }

    public function testCanInstantiateEnumClassFromKey()
    {
        $userType = UserType::fromKey('Administrator');
        $this->assertInstanceOf(UserType::class, $userType);
    }

    public function testAnExceptionIsThrownWhenTryingToInstantiateEnumClassWithAnInvalidEnumValue()
    {
        $this->expectException(InvalidEnumMemberException::class);

        UserType::fromValue('InvalidValue');
    }

    public function testAnExceptionIsThrownWhenTryingToInstantiateEnumClassWithAnInvalidEnumKey()
    {
        $this->expectException(InvalidEnumKeyException::class);

        UserType::fromKey('foobar');
    }

    public function testCanGetTheValueForAnEnumInstance()
    {
        $userType = UserType::fromValue(UserType::Administrator);

        $this->assertEquals($userType->value, UserType::Administrator);
    }

    public function testCanGetTheKeyForAnEnumInstance()
    {
        $userType = UserType::fromValue(UserType::Administrator);

        $this->assertEquals($userType->key, UserType::getKey(UserType::Administrator));
    }

    public function testCanGetTheDescriptionForAnEnumInstance()
    {
        $userType = UserType::fromValue(UserType::Administrator);

        $this->assertEquals($userType->description, UserType::getDescription(UserType::Administrator));
    }

    public function testCanGetEnumInstanceByCallingAnEnumKeyAsAStaticMethod()
    {
        $this->assertInstanceOf(UserType::class, UserType::Administrator());
    }

    public function testMagicInstantiationFromInstanceMethod()
    {
        $userType = new UserType(UserType::Administrator);
        $this->assertInstanceOf(UserType::class, $userType->magicInstantiationFromInstanceMethod());
    }

    public function testAnExceptionIsThrownWhenTryingToGetEnumInstanceByCallingAnEnumKeyAsAStaticMethodWhichDoesNotExist()
    {
        $this->expectException(InvalidEnumKeyException::class);

        UserType::KeyWhichDoesNotExist();
    }

    public function testGettingAnInstanceUsingAnInstanceReturnsAnInstance()
    {
        $this->assertInstanceOf(UserType::class, UserType::fromValue(UserType::Administrator));
    }
}
