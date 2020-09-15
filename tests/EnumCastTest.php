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

use HyperfExt\Enum\Exceptions\InvalidEnumMemberException;
use HyperfTest\Enum\Enums\UserType;
use HyperfTest\Enum\Models\Example;
use HyperfTest\Enum\Models\WithTraitButNoCasts;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EnumCastTest extends TestCase
{
    public function testModelCanDetectWhichAttributesToCastToAnEnum()
    {
        $model = new Example();

        $this->assertTrue($model->hasEnumCast('user_type'));
        $this->assertFalse($model->hasEnumCast('doesnt_exist'));
    }

    public function testCanSetModelValueUsingEnumInstance()
    {
        $model = new Example();
        $model->user_type = UserType::Moderator();

        $this->assertEquals(UserType::Moderator(), $model->user_type);
    }

    public function testCanSetModelValueUsingEnumValue()
    {
        $model = new Example();
        $model->user_type = UserType::Moderator;

        $this->assertEquals(UserType::Moderator(), $model->user_type);
    }

    public function testCannotSetModelValueUsingInvalidEnumValue()
    {
        $this->expectException(InvalidEnumMemberException::class);

        $model = new Example();
        $model->user_type = 5;
    }

    public function testGettingModelValueReturnsEnumInstance()
    {
        $model = new Example();
        $model->user_type = UserType::Moderator;

        $this->assertInstanceOf(UserType::class, $model->user_type);
    }

    public function testCanGetAndSetNullOnEnumCastable()
    {
        $model = new Example();
        $model->user_type = null;

        $this->assertNull($model->user_type);
    }

    public function testThatModelWithEnumCanBeCastToArray()
    {
        $model = new Example();
        $model->user_type = UserType::Moderator();

        $this->assertSame(['user_type' => 1], $model->toArray());
    }

    public function testModelWithTraitButNoCasts()
    {
        $model = new WithTraitButNoCasts();
        $model->foo = true;
        $this->assertTrue($model->foo);
    }
}
