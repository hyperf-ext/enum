<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfExt\Enum\Exceptions;

use Exception;
use HyperfExt\Enum\Enum;

class InvalidEnumMemberException extends Exception
{
    /**
     * Create an InvalidEnumMemberException.
     *
     * @param mixed $invalidValue
     */
    public function __construct($invalidValue, Enum $enum)
    {
        $invalidValueType = gettype($invalidValue);
        $enumValues = implode(', ', $enum::getValues());
        $enumClassName = class_basename($enum);

        parent::__construct("Cannot construct an instance of {$enumClassName} using the value ({$invalidValueType}) `{$invalidValue}`. Possible values are [{$enumValues}].");
    }
}
