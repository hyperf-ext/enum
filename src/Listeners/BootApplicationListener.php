<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfExt\Enum\Listeners;

use Doctrine\DBAL\Types\Type;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use HyperfExt\Enum\EnumType;

class BootApplicationListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event)
    {
        if (class_exists('Doctrine\DBAL\Types\Type')) {
            if (! Type::hasType(EnumType::ENUM)) {
                Type::addType(EnumType::ENUM, EnumType::class);
            }
        }
    }
}
