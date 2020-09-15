<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfExt\Enum\Contracts;

interface EnumInterface
{
    /**
     * Determine if this instance is equivalent to a given value.
     *
     * @param mixed $enumValue
     */
    public function is($enumValue): bool;
}
