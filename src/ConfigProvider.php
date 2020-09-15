<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfExt\Enum;

use HyperfExt\Enum\Commands\EnumAnnotateCommand;
use HyperfExt\Enum\Commands\MakeEnumCommand;
use HyperfExt\Enum\Listeners\BootApplicationListener;
use HyperfExt\Enum\Listeners\ValidatorFactoryResolvedListener;

class ConfigProvider
{
    public function __invoke(): array
    {
        $languagesPath = BASE_PATH . '/storage/languages';
        $translationConfigFile = BASE_PATH . '/config/autoload/translation.php';
        if (file_exists($translationConfigFile)) {
            $translationConfig = include $translationConfigFile;
            $languagesPath = $translationConfig['path'] ?? $languagesPath;
        }

        return [
            'commands' => [
                EnumAnnotateCommand::class,
                MakeEnumCommand::class,
            ],
            'listeners' => [
                BootApplicationListener::class,
                ValidatorFactoryResolvedListener::class,
            ],
            'publish' => [
                [
                    'id' => 'zh_CN',
                    'description' => 'The message bag for enum.',
                    'source' => __DIR__ . '/../publish/zh_CN/enum.php',
                    'destination' => $languagesPath . '/zh_CN/enum.php',
                ],
                [
                    'id' => 'en',
                    'description' => 'The message bag for enum.',
                    'source' => __DIR__ . '/../publish/en/enum.php',
                    'destination' => $languagesPath . '/en/enum.php',
                ],
            ],
        ];
    }
}
