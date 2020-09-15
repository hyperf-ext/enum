<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfTest\Enum\Enums\Annotate;

use HyperfExt\Enum\Enum;

final class AnnotateTestTwoEnum extends Enum
{
    const TEST = 'test';

    const XYZ = 'xyz';
}
