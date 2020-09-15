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
use HyperfTest\Enum\Enums\UserTypeCustomCast;
use HyperfTest\Enum\Models\NativeCastModel;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class NativeEnumCastTest extends TestCase
{
    public function testCanSetModelValueUsingEnumInstance()
    {
        $model = new NativeCastModel();
        $model->user_type = UserType::Moderator();

        $this->assertEquals(UserType::Moderator(), $model->user_type);
    }

    public function testCanSetModelValueUsingEnumValue()
    {
        $model = new NativeCastModel();
        $model->user_type = UserType::Moderator;

        $this->assertEquals(UserType::Moderator(), $model->user_type);
    }

    public function testCannotSetModelValueUsingInvalidEnumValue()
    {
        $this->expectException(InvalidEnumMemberException::class);

        $model = new NativeCastModel();
        $model->user_type = 5;
    }

    public function testGettingModelValueReturnsEnumInstance()
    {
        $model = new NativeCastModel();
        $model->user_type = UserType::Moderator;

        $this->assertInstanceOf(UserType::class, $model->user_type);
    }

    public function testCanGetAndSetNullOnEnumCastable()
    {
        $model = new NativeCastModel();
        $model->user_type = null;

        $this->assertNull($model->user_type);
    }

    public function testCanHandleStringIntFromDatabase()
    {
        /** @var NativeCastModel $model */
        $model = new NativeCastModel();

        $reflection = new \ReflectionProperty(NativeCastModel::class, 'attributes');
        $reflection->setAccessible(true);
        $reflection->setValue($model, ['user_type' => '1']);

        $this->assertInstanceOf(UserType::class, $model->user_type);
    }

    public function testThatModelWithEnumCanBeCastToArray()
    {
        $model = new NativeCastModel();
        $model->user_type = UserType::Moderator();

        $this->assertSame(['user_type' => [
            'value' => 1,
            'description' => 'Moderator',
        ]], $model->toArray());
    }

    public function testCanUseCustomCasting()
    {
        /** @var NativeCastModel $model */
        $model = new NativeCastModel();

        $reflection = new \ReflectionProperty(NativeCastModel::class, 'attributes');
        $reflection->setAccessible(true);
        $reflection->setValue($model, ['user_type_custom' => 'type-3']);

        $this->assertInstanceOf(UserTypeCustomCast::class, $model->user_type_custom);
        $this->assertEquals(UserTypeCustomCast::SuperAdministrator(), $model->user_type_custom);

        $model->user_type_custom = UserTypeCustomCast::Administrator();

        $this->assertSame('type-0', $reflection->getValue($model)['user_type_custom']);
    }

    public function testCanBailCustomCasting()
    {
        /** @var NativeCastModel $model */
        $model = new NativeCastModel();

        $reflection = new \ReflectionProperty(NativeCastModel::class, 'attributes');
        $reflection->setAccessible(true);
        $reflection->setValue($model, ['user_type_custom' => '']);

        $this->assertNull($model->user_type_custom);
    }
}
