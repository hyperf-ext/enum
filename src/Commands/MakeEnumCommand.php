<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfExt\Enum\Commands;

use Hyperf\Devtool\Generator\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeEnumCommand extends GeneratorCommand
{
    public function __construct()
    {
        parent::__construct('gen:enum');
        $this->setDescription('Create a new enum class');
        $this->addOption('flagged', 'F', InputOption::VALUE_NONE, 'Generate a flagged enum');
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        if ($this->input->getOption('flagged')) {
            return __DIR__ . '/stubs/flagged-enum.stub';
        }

        return __DIR__ . '/stubs/enum.stub';
    }

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace(): string
    {
        return $this->getConfig()['namespace'] ?? 'App\\Enum';
    }
}
