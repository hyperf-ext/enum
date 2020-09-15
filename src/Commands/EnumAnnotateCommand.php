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

use Hyperf\Utils\Filesystem\Filesystem;
use HyperfExt\Enum\Enum;
use Laminas\Code\Generator\DocBlock\Tag\MethodTag;
use Laminas\Code\Generator\DocBlock\Tag\TagInterface;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Reflection\DocBlockReflection;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class EnumAnnotateCommand extends AbstractAnnotationCommand
{
    const DEFAULT_SCAN_FOLDER = 'Enum';

    const PARENT_CLASS = Enum::class;

    protected $name = 'enum:annotate';

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct($filesystem);
        $this->setDescription('Generate DocBlock annotations for enum classes');
    }

    /**
     * Apply annotations to a reflected class.
     */
    protected function annotate(ReflectionClass $reflectionClass)
    {
        $docBlock = DocBlockGenerator::fromArray([]);
        $originalDocBlock = null;

        if (strlen((string) $reflectionClass->getDocComment()) !== 0) {
            $originalDocBlock = DocBlockGenerator::fromReflection(new DocBlockReflection($reflectionClass));
            $docBlock->setShortDescription($originalDocBlock->getShortDescription());
        }

        $this->updateClassDocblock($reflectionClass, $this->getDocBlock($reflectionClass));
    }

    protected function getDocblockTags(array $originalTags, ReflectionClass $reflectionClass): array
    {
        $constants = $reflectionClass->getConstants();

        $existingTags = array_filter($originalTags, function (TagInterface $tag) use ($constants) {
            return ! $tag instanceof MethodTag || ! in_array($tag->getMethodName(), array_keys($constants), true);
        });

        return collect($constants)
            ->map(function ($value, $constantName) {
                return new MethodTag($constantName, ['static'], null, true);
            })
            ->merge($existingTags)
            ->toArray();
    }

    protected function getClassFinder(): Finder
    {
        $finder = new Finder();
        $scanPath = $this->input->getOption('folder') ?? BASE_PATH . '/app/' . self::DEFAULT_SCAN_FOLDER;

        return $finder->files()->in($scanPath)->name('*.php');
    }
}
