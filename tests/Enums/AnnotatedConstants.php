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

final class AnnotatedConstants extends Enum
{
    /**
     * Internal and deprecated.
     *
     * @internal
     *
     * @deprecated 1.0 Deprecation description
     */
    const InternalDeprecated = 0;

    /**
     * Internal.
     *
     * @internal
     */
    const Internal = 1;

    /**
     * Deprecated.
     *
     * @deprecated
     */
    const Deprecated = 2;

    const Unannotated = 3;
}
