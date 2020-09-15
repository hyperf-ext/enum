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

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * This type allows you to call ->change() on an enum column definition
 * when using migrations.
 */
class EnumType extends Type
{
    const ENUM = 'enum';

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param mixed[] $fieldDeclaration the field declaration
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform the currently used database platform
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = implode(
            ',',
            array_map(
                function (string $value): string {
                    return "'{$value}'";
                },
                $fieldDeclaration['allowed']
            )
        );

        return "ENUM({$values})";
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return self::ENUM;
    }

    /**
     * @return string[]
     */
    public function getMappedDatabaseTypes(AbstractPlatform $platform)
    {
        return [
            self::ENUM,
        ];
    }
}
