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

use Hyperf\Contract\ContainerInterface;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\Translation\ArrayLoader;
use Hyperf\Translation\Translator;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Validation\ValidatorFactory;
use HyperfExt\Enum\Rules\Enum;
use HyperfExt\Enum\Rules\EnumKey;
use HyperfExt\Enum\Rules\EnumValue;
use HyperfTest\Enum\Enums\UserType;
use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EnumPipeValidationTest extends TestCase
{
    /**
     * Setup the database schema.
     */
    protected function setUp(): void
    {
        $container = m::mock(ContainerInterface::class);
        $container->shouldReceive('has')->andReturn(true);
        $translator = new Translator(
            new ArrayLoader(),
            'en'
        );
        $validatorFactory = new ValidatorFactory($translator, $container);
        $container->shouldReceive('get')->with(TranslatorInterface::class)->andReturn($translator);
        $container->shouldReceive('get')->with(ValidatorFactory::class)->andReturn($validatorFactory);
        ApplicationContext::setContainer($container);

        $validatorFactory->extend('enum_key', function ($attribute, $value, $parameters, $validator) {
            $enum = $parameters[0] ?? null;
            $enumKey = new EnumKey($enum);
            $validator->setCustomMessages(['enum_key' => $enumKey->message()]);
            return $enumKey->passes($attribute, $value);
        });

        $validatorFactory->extend('enum_value', function ($attribute, $value, $parameters, $validator) {
            $enum = $parameters[0] ?? null;

            $strict = $parameters[1] ?? null;

            if (! $strict) {
                return (new EnumValue($enum))->passes($attribute, $value);
            }

            $strict = (bool) json_decode(strtolower($strict));

            $enumValue = new EnumValue($enum, $strict);
            $validator->setCustomMessages(['enum_value' => $enumValue->message()]);

            return $enumValue->passes($attribute, $value);
        });

        $validatorFactory->extend('enum', function ($attribute, $value, $parameters, $validator) {
            $enum = $parameters[0] ?? null;

            $enum = new Enum($enum);
            $validator->setCustomMessages(['enum' => $enum->message()]);

            return $enum->passes($attribute, $value);
        });
    }

    /**
     * Tear down the database schema.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    public function testCanValidateValueUsingPipeValidation()
    {
        $factory = ApplicationContext::getContainer()->get(ValidatorFactory::class);
        $validator = $factory->make(
            ['type' => UserType::Administrator],
            ['type' => 'enum_value:' . UserType::class]
        );

        $this->assertTrue($validator->passes());

        $validator = $factory->make(
            ['type' => 99],
            ['type' => 'enum_value:' . UserType::class]
        );

        $this->assertFalse($validator->passes());
    }

    public function testCanValidateValueUsingPipeValidationWithoutStrictTypeChecking()
    {
        $factory = ApplicationContext::getContainer()->get(ValidatorFactory::class);
        $validator = $factory->make(['type' => (string) UserType::Administrator], [
            'type' => 'enum_value:' . UserType::class . ',false',
        ]);

        $this->assertTrue($validator->passes());
    }

    public function testCanValidateKeyUsingPipeValidation()
    {
        $factory = ApplicationContext::getContainer()->get(ValidatorFactory::class);
        $validator = $factory->make(['type' => UserType::getKey(UserType::Administrator)], [
            'type' => 'enum_key:' . UserType::class,
        ]);

        $this->assertTrue($validator->passes());

        $validator = $factory->make(['type' => 'wrong'], [
            'type' => 'enum_key:' . UserType::class,
        ]);

        $this->assertFalse($validator->passes());
    }

    public function testCanValidateEnumUsingPipeValidation()
    {
        $factory = ApplicationContext::getContainer()->get(ValidatorFactory::class);
        $validator = $factory->make(['type' => UserType::Administrator()], [
            'type' => 'enum:' . UserType::class,
        ]);

        $this->assertTrue($validator->passes());

        $validator = $factory->make(['type' => 'wrong'], [
            'type' => 'enum:' . UserType::class,
        ]);

        $this->assertFalse($validator->passes());
    }
}
