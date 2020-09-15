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

use hanneskod\classtools\Iterator\ClassIterator;
use Hyperf\Command\Command;
use Hyperf\Utils\Filesystem\Filesystem;
use InvalidArgumentException;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Reflection\DocBlockReflection;
use ReflectionClass;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;

abstract class AbstractAnnotationCommand extends Command
{
    public const PARENT_CLASS = null;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;

        $this->addArgument('class', InputArgument::OPTIONAL, 'The class name to generate annotations for');
        $this->addOption('folder', null, InputOption::VALUE_OPTIONAL, 'The folder to scan for classes to annotate');
    }

    /**
     * Handle the command call.
     *
     * @throws \ReflectionException
     * @return int
     */
    public function handle()
    {
        if ($className = $this->input->getArgument('class')) {
            $this->annotateClass($className);

            return 0;
        }

        $this->annotateFolder();

        return 0;
    }

    /**
     * Annotate classes in a given folder.
     */
    protected function annotateFolder()
    {
        $classes = new ClassIterator($this->getClassFinder());

        $classes->enableAutoloading();

        /** @var \ReflectionClass $reflection */
        foreach ($classes as $reflection) {
            if ($reflection->isSubclassOf(static::PARENT_CLASS)) {
                $this->annotate($reflection);
            }
        }
    }

    /**
     * Annotate a specific class by name.
     *
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    protected function annotateClass(string $className)
    {
        if (! is_subclass_of($className, static::PARENT_CLASS)) {
            throw new InvalidArgumentException(
                sprintf('The given class must be an instance of %s: %s', static::PARENT_CLASS, $className)
            );
        }

        $reflection = new ReflectionClass($className);
        $this->annotate($reflection);
    }

    /**
     * Write new DocBlock to the class.
     */
    protected function updateClassDocblock(ReflectionClass $reflectionClass, DocBlockGenerator $docBlock)
    {
        $shortName = $reflectionClass->getShortName();
        $fileName = $reflectionClass->getFileName();
        $contents = $this->filesystem->get($fileName);

        $classDeclaration = "class {$shortName}";

        if ($reflectionClass->isFinal()) {
            $classDeclaration = "final {$classDeclaration}";
        } elseif ($reflectionClass->isAbstract()) {
            $classDeclaration = "abstract {$classDeclaration}";
        }

        // Remove existing docblock
        $contents = preg_replace(
            sprintf('#([\n]?\/\*(?:[^*]|\n|(?:\*(?:[^\/]|\n)))*\*\/)?[\n]?%s#ms', preg_quote($classDeclaration)),
            "\n" . $classDeclaration,
            $contents
        );

        $classDeclarationOffset = strpos($contents, $classDeclaration);
        // Make sure we don't replace too much
        $contents = substr_replace(
            $contents,
            sprintf('%s%s', $docBlock->generate(), $classDeclaration),
            $classDeclarationOffset,
            strlen($classDeclaration)
        );

        $this->filesystem->put($fileName, $contents);
        $this->info("Wrote new phpDocBlock to {$fileName}.");
    }

    protected function getDocBlock(ReflectionClass $reflectionClass): DocBlockGenerator
    {
        $docBlock = DocBlockGenerator::fromArray([]);

        $originalDocBlock = null;

        if ($reflectionClass->getDocComment()) {
            $originalDocBlock = DocBlockGenerator::fromReflection(
                new DocBlockReflection(ltrim($reflectionClass->getDocComment()))
            );

            if ($originalDocBlock->getShortDescription()) {
                $docBlock->setShortDescription($originalDocBlock->getShortDescription());
            }

            if ($originalDocBlock->getLongDescription()) {
                $docBlock->setLongDescription($originalDocBlock->getLongDescription());
            }
        }

        $docBlock->setTags($this->getDocblockTags(
            $originalDocBlock ? $originalDocBlock->getTags() : [],
            $reflectionClass
        ));

        return $docBlock;
    }

    abstract protected function getDocblockTags(array $originalTags, ReflectionClass $reflectionClass): array;

    abstract protected function annotate(ReflectionClass $reflectionClass);

    abstract protected function getClassFinder(): Finder;
}
