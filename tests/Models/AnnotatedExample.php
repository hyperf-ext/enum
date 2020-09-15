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
use HyperfTest\Enum\Enums\UserType;

/**
 * Description should be kept.
 *
 * @property null|\HyperfTest\Enum\Enums\UserType $user_type
 */
class AnnotatedExample extends Model
{
    use CastsEnums;

    protected $enumCasts = [
        'user_type' => UserType::class,
    ];

    protected $fillable = [
        'user_type',
    ];
}
