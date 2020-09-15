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
use HyperfTest\Enum\Enums\UserType;
use HyperfTest\Enum\Enums\UserTypeCustomCast;

class NativeCastModel extends Model
{
    protected $casts = [
        'user_type' => UserType::class,
        'user_type_custom' => UserTypeCustomCast::class,
    ];

    protected $fillable = [
        'user_type',
        'user_type_custom',
    ];
}
