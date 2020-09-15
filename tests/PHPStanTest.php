<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfTest\Enum;

use HyperfExt\Enum\PHPStan\EnumMethodsClassReflectionExtension;
use HyperfTest\Enum\Enums\AnnotatedConstants;
use HyperfTest\Enum\Enums\UserType;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Testing\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PHPStanTest extends TestCase
{
    /**
     * @var \HyperfExt\Enum\PHPStan\EnumMethodsClassReflectionExtension
     */
    private $reflectionExtension;

    /**
     * @var \PHPStan\Reflection\ClassReflection
     */
    private $enumReflection;

    protected function setUp(): void
    {
        parent::setUp();

        $broker = $this->createBroker();
        $this->enumReflection = $broker->getClass(UserType::class);

        $this->reflectionExtension = new EnumMethodsClassReflectionExtension();
    }

    public function testRecognizesMagicStaticMethods()
    {
        $this->assertTrue(
            $this->reflectionExtension->hasMethod($this->enumReflection, 'Administrator')
        );

        $this->assertFalse(
            $this->reflectionExtension->hasMethod($this->enumReflection, 'FooBar')
        );
    }

    public function testGetEnumMethodReflection()
    {
        $this->assertInstanceOf(
            MethodReflection::class,
            $this->reflectionExtension->getMethod($this->enumReflection, 'Administrator')
        );
    }

    public function testEnumMethodReflectionHasSideEffectsReturnsNo(): void
    {
        $method = $this->getMethodReflection(UserType::class, 'Administrator');
        $this->assertTrue($method->hasSideEffects()->no(), 'hasSideEffects should return TrinaryLogic::No');
    }

    public function testEnumMethodReflectionIsFinalReturnsNo(): void
    {
        $method = $this->getMethodReflection(UserType::class, 'Administrator');
        $this->assertTrue($method->isFinal()->no(), 'isFinal should return TrinaryLogic::No');
    }

    public function testInternalDeprecatedConstantStaticMethodIsInternalAndDeprecated(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'InternalDeprecated');

        $this->assertTrue($method->isInternal()->yes(), 'isInternal should return TrinaryLogic::Yes');
        $this->assertTrue($method->isDeprecated()->yes(), 'isDeprecated should return TrinaryLogic::Yes');
    }

    public function testInternalDeprecatedConstantStaticMethodDeprecationMessage(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'InternalDeprecated');

        $deprecatedDescription = $method->getDeprecatedDescription();
        $this->assertEquals('1.0 Deprecation description', $deprecatedDescription);
    }

    public function testInternalConstantStaticMethodIsInternal(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Internal');

        $this->assertTrue($method->isInternal()->yes(), 'isInternal should return TrinaryLogic::Yes');
        $this->assertFalse($method->isDeprecated()->yes(), 'isDeprecated should return TrinaryLogic::No');
    }

    public function testDeprecatedConstantStaticMethodIsDeprecated(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Deprecated');

        $this->assertFalse($method->isInternal()->yes(), 'isInternal should return TrinaryLogic::No');
        $this->assertTrue($method->isDeprecated()->yes(), 'isDeprecated should return TrinaryLogic::Yes');
    }

    public function testDeprecatedConstantStaticMethodDeprecationMessage(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Deprecated');

        $deprecatedDescription = $method->getDeprecatedDescription();
        $this->assertEquals('', $deprecatedDescription);
    }

    public function testUnannotatedConstantStaticMethodIsNotInternalAndNotDeprecated(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Unannotated');

        $this->assertFalse($method->isInternal()->yes(), 'isInteral should return TrinaryLogic::No');
        $this->assertFalse($method->isDeprecated()->yes(), 'isDeprecated should return TrinaryLogic::No');
    }

    public function testUnnanotatedConstantStaticMethodDeprecatedMessageIsNull(): void
    {
        $method = $this->getMethodReflection(AnnotatedConstants::class, 'Unannotated');

        $this->assertNull($method->getDeprecatedDescription());
    }

    public function testGetVariantsReturnsArray(): void
    {
        $method = $this->getMethodReflection(UserType::class, 'Administrator');
        $this->assertIsArray($method->getVariants());
    }

    protected function getMethodReflection(string $class, string $name): MethodReflection
    {
        $broker = $this->createBroker();
        return $this->reflectionExtension->getMethod($broker->getClass($class), $name);
    }
}
