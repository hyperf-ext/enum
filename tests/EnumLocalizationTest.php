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
use HyperfTest\Enum\Enums\UserTypeLocalized;
use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EnumLocalizationTest extends TestCase
{
    /**
     * Setup the database schema.
     */
    protected function setUp(): void
    {
        $container = m::mock(ContainerInterface::class);
        $container->shouldReceive('has')->andReturn(true);
        $loader = new ArrayLoader();
        $loader->addMessages('en', 'enum', include __DIR__ . '/lang/en/enum.php');
        $loader->addMessages('es', 'enum', include __DIR__ . '/lang/es/enum.php');
        $translator = new Translator($loader, 'es');
        $container->shouldReceive('get')->with(TranslatorInterface::class)->andReturn($translator);
        ApplicationContext::setContainer($container);
    }

    /**
     * Tear down the database schema.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    public function testEnumGetDescriptionWithLocalization()
    {
        $translator = ApplicationContext::getContainer()->get(TranslatorInterface::class);

        $translator->setLocale('en');
        $this->assertEquals('Super administrator', UserTypeLocalized::getDescription(UserTypeLocalized::SuperAdministrator));

        $translator->setLocale('es');
        $this->assertEquals('Súper administrador', UserTypeLocalized::getDescription(UserTypeLocalized::SuperAdministrator));
    }

    public function testEnumGetDescriptionForMissingLocalizationKey()
    {
        $translator = ApplicationContext::getContainer()->get(TranslatorInterface::class);

        $translator->setLocale('en');
        $this->assertEquals('Moderator', UserTypeLocalized::getDescription(UserTypeLocalized::Moderator));

        $translator->setLocale('es');
        $this->assertEquals('Moderator', UserTypeLocalized::getDescription(UserTypeLocalized::Moderator));
    }
}
