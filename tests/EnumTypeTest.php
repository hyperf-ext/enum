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

use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Types\Type;
use HyperfExt\Enum\EnumType;
use HyperfTest\Enum\Enums\StringValues;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EnumTypeTest extends TestCase
{
    public function testGetSQLDeclaration(): void
    {
        if (class_exists('Doctrine\DBAL\Types\Type')) {
            if (! Type::hasType(EnumType::ENUM)) {
                Type::addType(EnumType::ENUM, EnumType::class);
            }
        }

        $enumType = Type::getType(EnumType::ENUM);

        $this->assertSame(
            "ENUM('administrator','moderator')",
            $enumType->getSQLDeclaration(
                ['allowed' => StringValues::getValues()],
                new MySQL57Platform()
            )
        );
    }
}
