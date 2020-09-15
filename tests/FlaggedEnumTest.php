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

use HyperfTest\Enum\Enums\SuperPowers;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class FlaggedEnumTest extends TestCase
{
    public function testCanConstructFlaggedEnumUsingStaticProperties()
    {
        /** @var SuperPowers $powers */
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight, SuperPowers::LaserVision]);
        $this->assertInstanceOf(SuperPowers::class, $powers);

        $powers = SuperPowers::fromValue([SuperPowers::Strength, SuperPowers::Flight, SuperPowers::LaserVision]);
        $this->assertInstanceOf(SuperPowers::class, $powers);

        $powers = SuperPowers::flags([SuperPowers::Strength, SuperPowers::Flight, SuperPowers::LaserVision]);
        $this->assertInstanceOf(SuperPowers::class, $powers);
    }

    public function testCanConstructFlaggedEnumUsingInstances()
    {
        /** @var SuperPowers $powers */
        $powers = new SuperPowers([SuperPowers::Strength(), SuperPowers::Flight(), SuperPowers::LaserVision()]);
        $this->assertInstanceOf(SuperPowers::class, $powers);

        $powers = SuperPowers::fromValue([SuperPowers::Strength(), SuperPowers::Flight(), SuperPowers::LaserVision()]);
        $this->assertInstanceOf(SuperPowers::class, $powers);

        $powers = SuperPowers::flags([SuperPowers::Strength(), SuperPowers::Flight(), SuperPowers::LaserVision()]);
        $this->assertInstanceOf(SuperPowers::class, $powers);
    }

    public function testCanCheckIfInstanceHasFlag()
    {
        /** @var SuperPowers $powers */
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertTrue($powers->hasFlag(SuperPowers::Strength()));
        $this->assertTrue($powers->hasFlag(SuperPowers::Strength));
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision()));
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision));
    }

    public function testCanCheckIfInstanceHasFlags()
    {
        /** @var SuperPowers $powers */
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertTrue($powers->hasFlags([SuperPowers::Strength, SuperPowers::Flight]));
        $this->assertFalse($powers->hasFlags([SuperPowers::Strength, SuperPowers::Invisibility]));
    }

    public function testCanCheckIfInstanceDoesNotHaveFlag()
    {
        /** @var SuperPowers $powers */
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertTrue($powers->notHasFlag(SuperPowers::LaserVision()));
        $this->assertTrue($powers->notHasFlag(SuperPowers::LaserVision));
        $this->assertFalse($powers->notHasFlag(SuperPowers::Strength()));
        $this->assertFalse($powers->notHasFlag(SuperPowers::Strength));
    }

    public function testCanCheckIfInstanceDoesNotHaveFlags()
    {
        /** @var SuperPowers $powers */
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertTrue($powers->notHasFlags([SuperPowers::Invisibility, SuperPowers::LaserVision]));
        $this->assertFalse($powers->notHasFlags([SuperPowers::Strength, SuperPowers::LaserVision]));
        $this->assertFalse($powers->notHasFlags([SuperPowers::Strength, SuperPowers::Flight]));
    }

    public function testCanSetFlags()
    {
        /** @var SuperPowers $powers */
        $powers = SuperPowers::None();
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision));

        $powers->setFlags([SuperPowers::LaserVision, SuperPowers::Strength]);
        $this->assertTrue($powers->hasFlag(SuperPowers::LaserVision));
        $this->assertTrue($powers->hasFlag(SuperPowers::Strength));
    }

    public function testCanAddFlag()
    {
        /** @var SuperPowers $powers */
        $powers = SuperPowers::None();
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision));

        $powers->addFlag(SuperPowers::LaserVision);
        $this->assertTrue($powers->hasFlag(SuperPowers::LaserVision));

        $powers->addFlag(SuperPowers::Strength);
        $this->assertTrue($powers->hasFlag(SuperPowers::Strength));
    }

    public function testCanAddFlags()
    {
        /** @var SuperPowers $powers */
        $powers = SuperPowers::None();
        $this->assertFalse($powers->hasFlag(SuperPowers::LaserVision));

        $powers->addFlags([SuperPowers::LaserVision, SuperPowers::Strength]);
        $this->assertTrue($powers->hasFlags([SuperPowers::LaserVision, SuperPowers::Strength]));
    }

    public function testCanRemoveFlag()
    {
        /** @var SuperPowers $powers */
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);
        $this->assertTrue($powers->hasFlags([SuperPowers::Strength, SuperPowers::Flight]));

        $powers->removeFlag(SuperPowers::Strength);
        $this->assertFalse($powers->hasFlag(SuperPowers::Strength));

        $powers->removeFlag(SuperPowers::Flight);
        $this->assertFalse($powers->hasFlag(SuperPowers::Flight));

        $this->assertTrue($powers->is(SuperPowers::None));
    }

    public function testCanRemoveFlags()
    {
        /** @var SuperPowers $powers */
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);
        $this->assertTrue($powers->hasFlags([SuperPowers::Strength, SuperPowers::Flight]));

        $powers->removeFlags([SuperPowers::Strength, SuperPowers::Flight]);
        $this->assertFalse($powers->hasFlags([SuperPowers::Strength, SuperPowers::Flight]));

        $this->assertTrue($powers->is(SuperPowers::None));
    }

    public function testCanGetFlags()
    {
        /** @var SuperPowers $powers */
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight, SuperPowers::Invisibility]);
        $flags = $powers->getFlags();

        $this->assertCount(3, $flags);
        $this->assertContainsOnlyInstancesOf(SuperPowers::class, $flags);
    }

    public function testCanSetShortcutValues()
    {
        /** @var SuperPowers $powers */
        $powers = new SuperPowers(SuperPowers::Superman);

        $this->assertTrue($powers->hasFlag(SuperPowers::Strength));
        $this->assertTrue($powers->hasFlag(SuperPowers::LaserVision));
        $this->assertFalse($powers->hasFlag(SuperPowers::Invisibility));
    }

    public function testShortcutValuesAreComparableToExplicitSet()
    {
        /** @var SuperPowers $powers */
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::LaserVision, SuperPowers::Flight]);
        $this->assertTrue($powers->hasFlag(SuperPowers::Superman));

        $powers->removeFlag([SuperPowers::LaserVision]);
        $this->assertFalse($powers->hasFlag(SuperPowers::Superman));
    }

    public function testCanCheckIfInstanceHasMultipleFlagsSet()
    {
        $this->assertTrue(SuperPowers::Superman()->hasMultipleFlags());
        $this->assertFalse(SuperPowers::Strength()->hasMultipleFlags());
        $this->assertFalse(SuperPowers::None()->hasMultipleFlags());
    }

    public function testCanGetBitmaskForAnInstance()
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);
        $this->assertEquals(1001, $powers->getBitmask());

        $this->assertEquals(1101, SuperPowers::Superman()->getBitmask());
    }

    public function testCanInstantiateAFlaggedEnumFromAValueWhichHasMultipleFlagsSet()
    {
        $powers = new SuperPowers([SuperPowers::Strength, SuperPowers::Flight]);

        $this->assertEquals($powers, SuperPowers::fromValue($powers->value));
    }
}
