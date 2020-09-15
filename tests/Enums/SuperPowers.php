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

use HyperfExt\Enum\FlaggedEnum;

final class SuperPowers extends FlaggedEnum
{
    const Flight = 1 << 0;

    const Invisibility = 1 << 1;

    const LaserVision = 1 << 2;

    const Strength = 1 << 3;

    const Teleportation = 1 << 4;

    const Immortality = 1 << 5;

    const TimeTravel = 1 << 6;

    const Superman = self::Flight | self::Strength | self::LaserVision;
}
