<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
use HyperfTest\Enum\Enums\UserTypeLocalized;

return [
    UserTypeLocalized::class => [
        UserTypeLocalized::Administrator => 'Administrador',
        UserTypeLocalized::SuperAdministrator => 'Súper administrador',
    ],
];
