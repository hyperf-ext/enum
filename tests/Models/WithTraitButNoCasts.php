<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfTest\Enum\Models;

use Hyperf\Database\Model\Model;
use HyperfExt\Enum\Traits\CastsEnums;

class WithTraitButNoCasts extends Model
{
    use CastsEnums;
}
