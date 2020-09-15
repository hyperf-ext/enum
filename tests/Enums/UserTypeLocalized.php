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

use HyperfExt\Enum\Contracts\LocalizedEnum;
use HyperfExt\Enum\Enum;

final class UserTypeLocalized extends Enum implements LocalizedEnum
{
    const Moderator = 0;

    const Administrator = 1;

    const SuperAdministrator = 2;
}
