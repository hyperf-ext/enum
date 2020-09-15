<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfExt\Enum\Listeners;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Validation\Event\ValidatorFactoryResolved;
use HyperfExt\Enum\Rules\Enum;
use HyperfExt\Enum\Rules\EnumKey;
use HyperfExt\Enum\Rules\EnumValue;

class ValidatorFactoryResolvedListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            ValidatorFactoryResolved::class,
        ];
    }

    public function process(object $event)
    {
        /** @var \Hyperf\Validation\Contract\ValidatorFactoryInterface $validatorFactory */
        $validatorFactory = $event->validatorFactory;

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
}
