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

use HyperfTest\Enum\Enums\UserType;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EnumTypeHintTest extends TestCase
{
    public function testCanPassAnEnumInstanceToATypeHintedMethod()
    {
        $userType1 = UserType::fromValue(UserType::SuperAdministrator);
        $userType2 = UserType::fromValue(UserType::Moderator);

        $this->assertTrue($this->typeHintedMethod($userType1));
        $this->assertFalse($this->typeHintedMethod($userType2));
    }

    private function typeHintedMethod(UserType $userType)
    {
        if ($userType->is(UserType::SuperAdministrator)) {
            return true;
        }

        return false;
    }
}
