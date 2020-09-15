<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfTest\Enum\Enums;

use HyperfExt\Enum\Enum;

final class UserType extends Enum
{
    const Administrator = 0;

    const Moderator = 1;

    const Subscriber = 2;

    const SuperAdministrator = 3;

    public function magicInstantiationFromInstanceMethod(): self
    {
        return self::Administrator();
    }
}
