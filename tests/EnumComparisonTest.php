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

use HyperfTest\Enum\Enums\IntegerValues;
use HyperfTest\Enum\Enums\StringValues;
use HyperfTest\Enum\Enums\UserType;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EnumComparisonTest extends TestCase
{
    public function testComparisonAgainstPlainValueMatching()
    {
        $admin = UserType::fromValue(UserType::Administrator);

        $this->assertTrue($admin->is(UserType::Administrator));
    }

    public function testComparisonAgainstPlainValueNotMatching()
    {
        $admin = UserType::fromValue(UserType::Administrator);

        $this->assertFalse($admin->is(UserType::SuperAdministrator));
        $this->assertFalse($admin->is('some-random-value'));
        $this->assertTrue($admin->isNot(UserType::SuperAdministrator));
        $this->assertTrue($admin->isNot('some-random-value'));
    }

    public function testComparisonAgainstItselfMatches()
    {
        $admin = UserType::fromValue(UserType::Administrator);

        $this->assertTrue($admin->is($admin));
    }

    public function testComparisonAgainstOtherInstancesMatches()
    {
        $admin = UserType::fromValue(UserType::Administrator);
        $anotherAdmin = UserType::fromValue(UserType::Administrator);

        $this->assertTrue($admin->is($anotherAdmin));
    }

    public function testComparisonAgainstOtherInstancesNotMatching()
    {
        $admin = UserType::fromValue(UserType::Administrator);
        $superAdmin = UserType::fromValue(UserType::SuperAdministrator);

        $this->assertFalse($admin->is($superAdmin));
    }

    public function testEnumInstanceInArray()
    {
        $administrator = new StringValues(StringValues::Administrator);

        $this->assertTrue($administrator->in([
            StringValues::Moderator,
            StringValues::Administrator,
        ]));
        $this->assertTrue($administrator->in([
            new StringValues(StringValues::Moderator),
            new StringValues(StringValues::Administrator),
        ]));
        $this->assertTrue($administrator->in([StringValues::Administrator]));
        $this->assertFalse($administrator->in([StringValues::Moderator]));
    }

    /**
     * @test
     * Verify that relational comparision of Enum object uses attribute `$value`
     *
     * "comparison operation stops and returns at the first unequal property found."
     * as stated in https://www.php.net/manual/en/language.oop5.object-comparison.php#98725
     */
    public function testObjectRelationalComparison()
    {
        $b = IntegerValues::B();
        $a = IntegerValues::A();

        $this->assertTrue($a > $b);
    }
}
